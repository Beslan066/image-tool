<?php

namespace App\Services;

use YooKassa\Client;
use App\Models\User;

class YooKassaService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(
            config('services.yookassa.shop_id'),
            config('services.yookassa.secret_key')
        );
    }

    public function createPayment(User $user, string $plan, string $returnUrl)
    {
        $prices = [
            'monthly' => 299,
            'yearly' => 1990,
        ];

        $amount = $prices[$plan];
        $description = "Premium подписка - " . ($plan === 'monthly' ? '1 месяц' : '1 год');

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
