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

        // Для локальной разработки - имитируем успешную оплату
        if (app()->environment('local')) {
            // Активируем премиум сразу
            $user->activatePremium($plan['months']);

            // Сохраняем запись о подписке
            Subscription::create([
                'user_id' => $user->id,
                'payment_id' => 'local_' . uniqid(),
                'plan' => $request->plan,
                'amount' => $amount,
                'status' => 'paid',
                'paid_at' => now(),
                'expires_at' => now()->addMonths($plan['months']),
            ]);

            $redirectUrl = $request->input('redirect', route('converter'));
            return redirect($redirectUrl)->with('success', 'Premium подписка активирована!');
        }

        // Для production - реальная оплата через ЮKassa
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

            return redirect($payment['confirmation_url']);

        } catch (\Exception $e) {
            Log::error('Payment creation error: ' . $e->getMessage());
            return back()->with('error', 'Ошибка при создании платежа. Попробуйте позже.');
        }
    }

    public function success(Request $request)
    {
        $redirectUrl = $request->input('redirect', route('converter'));
        return redirect($redirectUrl)->with('success', 'Оплата прошла успешно! Premium подписка активирована.');
    }

    public function cancel(Request $request)
    {
        $redirectUrl = $request->input('redirect', route('converter'));
        return redirect($redirectUrl)->with('error', 'Оплата отменена');
    }

    public function webhook(Request $request)
    {
        Log::info('YooKassa webhook received', $request->all());

        if (app()->environment('local')) {
            return response()->json(['ok' => true]);
        }

        try {
            $payload = $request->all();
            $result = $this->yookassa->handleWebhook($payload);

            if ($result['success']) {
                DB::transaction(function () use ($result) {
                    $subscription = Subscription::where('payment_id', $result['payment_id'])->first();

                    if ($subscription && $subscription->status !== 'paid') {
                        $subscription->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);

                        $user = User::find($result['user_id']);
                        if ($user) {
                            $months = $result['plan'] === 'yearly' ? 12 : 1;
                            $user->activatePremium($months);
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
