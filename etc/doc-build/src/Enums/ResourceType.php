<?php

declare(strict_types=1);

namespace Oft\Generator\Enums;

final class ResourceType extends AbstractEnum
{
    public const CURRENCY = 'currency';
    public const CURRENCY_CATEGORY = 'currency_category';
    public const CURRENCY_TYPE = 'currency_type';
    public const PAYOUT_SERVICE = 'payout_service';
    public const PAYOUT_METHOD = 'payout_method';
    public const PAYOUT_METHOD_CATEGORY = 'payout_method_category';
    public const PAYMENT_SERVICE = 'payment_service';
    public const PAYMENT_METHOD = 'payment_method';
    public const PAYMENT_METHOD_CATEGORY = 'payment_method_category';
    public const PAYMENT_FLOW = 'payment_flow';
    public const VENDOR = 'vendor';
    public const PAYMENT_PROVIDER = 'payment_provider';
    public const COUNTRY = 'country';
}
