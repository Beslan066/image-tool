<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use App\Services\YooKassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $yookassa;

    public function __construct(YooKassaService $yookassa = null)
    {
        $this->yookassa = $yookassa;
    }

    public function checkout(Request $request)
    {

        Log::info('=== CHECKOUT DEBUG ===', [
            'yookassa_exists' => $this->yookassa ? 'yes' : 'no',
            'shop_id' => config('services.yookassa.shop_id'),
            'secret_key_set' => config('services.yookassa.secret_key') ? 'yes' : 'no'
        ]);


        $request->validate([
            'plan' => 'required|in:monthly,yearly,premium',
            'redirect' => 'nullable|url'
        ]);

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Необходимо авторизоваться для оформления подписки');
        }

        $plans = [
            'monthly' => ['price' => 299, 'months' => 1, 'name' => '1 месяц'],
            'yearly' => ['price' => 1990, 'months' => 12, 'name' => '1 год'],
            'premium' => ['price' => 299, 'months' => 1, 'name' => 'Premium'],
        ];

        $plan = $plans[$request->plan];
        $amount = $plan['price'];

        // Убираем мгновенную активацию для local!
        // Теперь даже на local будет настоящий тестовый платеж через ЮKassa

        if (!$this->yookassa) {
            Log::error('YooKassa service not initialized');
            return back()->with('error', 'Платежная система временно недоступна. Попробуйте позже.');
        }

        try {
            $returnUrl = $request->input('redirect', route('payment.success'));
            $payment = $this->yookassa->createPayment($user, $request->plan, $returnUrl);

            Subscription::create([
                'user_id' => $user->id,
                'payment_id' => $payment['payment_id'],
                'plan' => $request->plan,
                'amount' => $amount,
                'status' => 'pending',
                'expires_at' => now()->addHour(),
            ]);

            Log::info('Redirecting to YooKassa payment page', [
                'confirmation_url' => $payment['confirmation_url'],
                'payment_id' => $payment['payment_id']
            ]);

            // Перенаправляем на платежную страницу ЮKassa
            return redirect($payment['confirmation_url']);

        } catch (\Exception $e) {
            Log::error('Payment creation error: ' . $e->getMessage());
            return back()->with('error', 'Ошибка при создании платежа: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        Log::info('Payment success callback', [
            'user_id' => $request->user()?->id,
            'redirect' => $request->input('redirect'),
            'all_params' => $request->all()
        ]);

        $user = $request->user();
        $redirectUrl = $request->input('redirect', route('converter'));

        if (!$user) {
            Log::warning('Payment success: user not authenticated');
            return redirect($redirectUrl)->with('error', 'Пользователь не авторизован');
        }

        // Ищем последний PENDING платеж для этого пользователя
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('id', 'desc')
            ->first();

        if ($subscription) {
            Log::info('Found pending subscription, activating manually', [
                'subscription_id' => $subscription->id,
                'payment_id' => $subscription->payment_id,
                'plan' => $subscription->plan
            ]);

            // Активируем подписку
            $months = $subscription->plan === 'yearly' ? 12 : 1;
            $user->activatePremium($months);

            // Обновляем статус платежа
            $subscription->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            Log::info('Subscription activated via success callback', [
                'user_id' => $user->id,
                'premium_until' => $user->premium_until
            ]);

            return redirect($redirectUrl)->with('success', 'Premium подписка активирована!');
        }

        // Если нет pending платежа, проверяем, может уже активировано
        if ($user->isPremiumActive()) {
            return redirect($redirectUrl)->with('success', 'Premium подписка уже активна');
        }

        Log::warning('No pending subscription found for user', ['user_id' => $user->id]);
        return redirect($redirectUrl)->with('error', 'Не удалось активировать подписку. Обратитесь в поддержку.');
    }

    public function cancel(Request $request)
    {
        $redirectUrl = $request->input('redirect', route('converter'));
        return redirect($redirectUrl)->with('error', 'Оплата отменена');
    }

    public function webhook(Request $request)
    {
        Log::info('YooKassa webhook received', $request->all());

        if (!$this->yookassa) {
            Log::error('YooKassa service not initialized for webhook');
            return response()->json(['error' => 'Service unavailable'], 500);
        }

        try {
            $payload = $request->all();
            $event = $payload['event'] ?? null;

            if ($event === 'payment.succeeded') {
                $paymentId = $payload['object']['id'];
                $metadata = $payload['object']['metadata'];

                DB::transaction(function () use ($paymentId, $metadata) {
                    $subscription = Subscription::where('payment_id', $paymentId)->first();

                    if ($subscription && $subscription->status !== 'paid') {
                        $subscription->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        $user = User::find($metadata['user_id'] ?? $subscription->user_id);
                        if ($user) {
                            $months = ($metadata['plan'] ?? $subscription->plan) === 'yearly' ? 12 : 1;
                            $user->activatePremium($months);
                            Log::info('Subscription activated via webhook', ['user_id' => $user->id]);
                        }
                    }
                });
            }

            return response()->json(['ok' => true]);

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
