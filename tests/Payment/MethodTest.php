<?php

declare(strict_types=1);

namespace Tests\Payment;

use Oft\Generator\Enums\ResourceType;
use Tests\AbstractDataTest;
use Tests\Relation;

final class MethodTest extends AbstractDataTest
{
    public function test_check_methods_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYMENT_METHOD,
            'Duplicate payment methods:'
        );
    }

    public function test_check_method_categories_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYMENT_METHOD_CATEGORY,
            'Duplicate categories of payment methods:'
        );
    }

    public function test_method_with_category_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_METHOD,
            'category',
            ResourceType::PAYMENT_METHOD_CATEGORY
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'PAYMENT METHOD CATEGORY'),
            'in payment methods'
        );
    }

    public function test_method_with_vendor_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_METHOD,
            'vendor',
            ResourceType::VENDOR
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'VENDOR'),
            'in payment methods'
        );
    }

    public function test_method_with_countries_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_METHOD,
            'countries',
            ResourceType::COUNTRY
        );

        $this->assertCorrectRelationWithMany(
            $relation,
            'Payment methods with unknown countries:',
            'country codes'
        );
    }
}