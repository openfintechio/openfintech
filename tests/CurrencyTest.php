<?php

declare(strict_types=1);

namespace Tests;

use Oft\Generator\Enums\ResourceType;

final class CurrencyTest extends AbstractDataTest
{
    public function test_check_currencies_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::CURRENCY,
            'Duplicate currencies:'
        );
    }

    public function test_check_currency_types_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::CURRENCY_TYPE,
            'Duplicate types of currencies:'
        );
    }

    public function test_check_currency_categories_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::CURRENCY_CATEGORY,
            'Duplicate categories of currencies:'
        );
    }

    public function test_currency_with_currency_type_relation(): void
    {
        $relation = new Relation(
            ResourceType::CURRENCY,
            'type',
            ResourceType::CURRENCY_TYPE
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'CURRENCY TYPE'),
            'in currencies'
        );
    }

    public function test_currency_with_currency_category_relation(): void
    {
        $relation = new Relation(
            ResourceType::CURRENCY,
            'category',
            ResourceType::CURRENCY_CATEGORY
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'CURRENCY CATEGORY'),
            'in currencies'
        );
    }
}
