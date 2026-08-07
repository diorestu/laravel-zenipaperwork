<?php

namespace App\Services;

use App\Models\SystemSetting;

class PaymentGatewayResolver
{
    public function __construct(
        protected PakasirPaymentGateway $pakasir,
        protected SumopodPaymentGateway $sumopod
    ) {}

    public function getActiveGatewayName(): string
    {
        return SystemSetting::get('active_payment_gateway', config('services.payment_gateway.active', 'pakasir'));
    }

    public function getActiveGateway(): PakasirPaymentGateway|SumopodPaymentGateway
    {
        $active = $this->getActiveGatewayName();

        return match ($active) {
            'sumopod' => $this->sumopod,
            default => $this->pakasir,
        };
    }
}
