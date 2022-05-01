<?php

declare(strict_types=1);

namespace Tests\Payout;

use Oft\Generator\Enums\ResourceType;
use Tests\AbstractDataTest;
use Tests\Relation;

final class MethodTest extends AbstractDataTest
{
    public function test_check_methods_for_duplicates():void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYOUT_METHOD,
            'Duplicate payout methods:'
        );
    }

    public function test_check_method_categories_for_duplicates():void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYOUT_METHOD_CATEGORY,
            'Duplicate categories of payout methods:'
        );
    }

    public function test_method_with_category_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYOUT_METHOD,
            'category',
            ResourceType::PAYOUT_METHOD_CATEGORY
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'PAYOUT METHOD CATEGORY'),
            'in payout methods'
        );
    }

    public function test_method_with_vendor_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYOUT_METHOD,
            'vendor',
            ResourceType::VENDOR
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'VENDOR'),
            'in payout methods'
        );
    }

    public function test_method_with_countries_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYOUT_METHOD,
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
