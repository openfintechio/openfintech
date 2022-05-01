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

    public function test_payment_provider_with_countries_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_PROVIDER,
            'countries',
            ResourceType::COUNTRY
        );

        $this->assertCorrectRelationWithMany(
            $relation,
            'Providers with unknown countries:',
            'country codes'
        );
    }
}
