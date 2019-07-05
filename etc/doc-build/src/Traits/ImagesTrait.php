<?php

namespace Oft\Generator\Traits;

trait ImagesTrait
{

    private function getExt(string $code, string $resource_type, string $type): string
    {
        $file = __DIR__."/../../../../resources/$resource_type/$code/$type.png";
        return file_exists($file) ? 'png' : 'svg';
    }

    private function getProviderLogo(string $code, string $width = '400'): string
    {
        $ext = $this->getExt($code, 'payment_providers', 'logo');
        return "https://static.openfintech.io/payment_providers/$code/logo.$ext?w=$width&c=v0.59.26#w100";
    }

    private function getProviderIcon(string $code, string $width = '278'): string
    {
        $ext = $this->getExt($code, 'payment_providers', 'icon');
        return "https://static.openfintech.io/payment_providers/$code/icon.$ext?w=$width&c=v0.59.26#w100";
    }

    private function getFlagIcon(string $code): string
    {
        return "https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/flags/4x3/$code.svg#w24";
    }

    private function getPayoutMethodIcon(string $code, string $width = '278'): string
    {
        $ext = $this->getExt($code, 'payout_methods', 'icon');
        return "https://static.openfintech.io/payout_methods/$code/icon.$ext?w=$width&c=v0.59.26#w40";
    }

    private function getPayoutMethodLogo(string $code, string $width = '400'): string
    {
        $ext = $this->getExt($code, 'payout_methods', 'logo');
        return "https://static.openfintech.io/payout_methods/$code/logo.$ext?w=$width&c=v0.59.26#w24";
    }

    private function getPaymentMethodLogo(string $code, string $width = '400'): string
    {
        $ext = $this->getExt($code, 'payment_methods', 'logo');
        return "https://static.openfintech.io/payment_methods/$code/logo.$ext?w=$width&c=v0.59.26#w200";
    }

    private function getPaymentMethodIcon(string $code, string $width = '278'): string
    {
        $ext = $this->getExt($code, 'payment_methods', 'icon');
        return "https://static.openfintech.io/payment_methods/$code/icon.$ext?w=$width&c=v0.59.26#w100";
    }

    private function getCurrencyIcon(string $code, string $width = '278'): string
    {
        $ext = $this->getExt($code, 'currencies', 'icon');
        return "https://static.openfintech.io/currencies/$code/icon.$ext?w=$width&c=v0.59.26#w100";
    }

    private function getVendorLogo(string $code, string $width = '400'): string
    {
        $ext = $this->getExt($code, 'vendors', 'logo');
        return "https://static.openfintech.io/vendors/$code/logo.$ext?w=$width&c=v0.59.26#w200";
    }

    private function getVendorIcon(string $code, string $width = '278'): string
    {
        $ext = $this->getExt($code, 'vendors', 'icon');
        return "https://static.openfintech.io/vendors/$code/icon.$ext?w=$width&c=v0.59.26#w100";
    }
}