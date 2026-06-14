<?php

namespace App\Services;

use YooKassa\Client;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class YooKassaService
{
    protected $client;
    protected $isAvailable;

    public function __construct()
    {
        $this->isAvailable = false;

        try {
            // Используем config(), а не env()!
            $shopId = config('services.yookassa.shop_id');
            $secretKey = config('services.yookassa.secret_key');

            Log::info('YooKassa init', [
                'shop_id' => $shopId,
                'secret_key_length' => $secretKey ? strlen($secretKey) : 0
            ]);

            if ($shopId && $secretKey) {
                $this->client = new Client();
                $this->client->setAuth($shopId, $secretKey);
                $this->isAvailable = true;
                Log::info('YooKassa initialized successfully');
            } else {
                Log::warning('YooKassa not configured: missing credentials');
            }
        } catch (\Exception $e) {
            Log::error('YooKassa initialization error: ' . $e->getMessage());
        }
    }

    public function isAvailable()
    {
        return $this->isAvailable;
    }

    public function createPayment(User $user, string $plan, string $returnUrl)
    {
        if (!$this->isAvailable) {
            throw new \Exception('YooKassa service not available');
        }

        $prices = [
            'monthly' => 299,
            'yearly' => 1990,
            'premium' => 299,
        ];

        $amount = $prices[$plan];
        $description = "Premium подписка - " . ($plan === 'yearly' ? '1 год' : '1 месяц');

        $payment = $this->client->createPayment([
            'amount' => [
                'value' => $amount,
                'currency' => 'RUB',
            ],
            'confirmation' => [
                'type' => 'redirect',
                'return_url' => $returnUrl,
            ],
            'capture' => true,
            'description' => $description,
            'metadata' => [
                'user_id' => $user->id,
                'plan' => $plan,
                'email' => $user->email,
            ],
        ], uniqid('', true));

        return [
            'payment_id' => $payment->getId(),
            'confirmation_url' => $payment->getConfirmation()->getConfirmationUrl(),
        ];
    }

    public function getPaymentInfo($paymentId)
    {
        if (!$this->isAvailable) {
            throw new \Exception('YooKassa service not available');
        }
        return $this->client->getPaymentInfo($paymentId);
    }

    public function handleWebhook($payload)
    {
        $event = $payload['event'] ?? null;

        if ($event === 'payment.succeeded') {
            $paymentId = $payload['object']['id'];
            $metadata = $payload['object']['metadata'];

            return [
                'success' => true,
                'payment_id' => $paymentId,
                'user_id' => $metadata['user_id'] ?? null,
                'plan' => $metadata['plan'] ?? 'monthly',
            ];
        }

        return ['success' => false];
    }
}
