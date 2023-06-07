<?php

declare(strict_types=1);

namespace Tests;

use Oft\Generator\Dto\ProviderDto;
use Oft\Generator\Enums\ResourceType;

final class PaymentProviderTest extends AbstractDataTest
{
    public function test_check_payment_providers_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYMENT_PROVIDER,
            'Duplicate payment providers:'
        );
    }

    public function test_payment_provider_with_vendor_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_PROVIDER,
            'vendor',
            ResourceType::VENDOR
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'VENDOR'),
            'in payment providers'
        );
    }

    public function test_payment_provider_with_payment_method_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_PROVIDER,
            'paymentMethod',
            ResourceType::PAYMENT_METHOD
        );

        $this->assertCorrectRelationWithMany(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'PAYMENT_METHOD'),
            'in payment providers',
        );
    }

    public function test_payment_provider_with_payout_method_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_PROVIDER,
            'payoutMethod',
            ResourceType::PAYOUT_METHOD
        );

        $this->assertCorrectRelationWithMany(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'PAYOUT_METHOD'),
            'in payout providers',
        );
    }
}
