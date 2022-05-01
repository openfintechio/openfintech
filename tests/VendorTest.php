<?php

declare(strict_types=1);

namespace Tests;

use Oft\Generator\Enums\ResourceType;

final class VendorTest extends AbstractDataTest
{
    public function test_chech_vendors_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::VENDOR,
            'Duplicate vendors:'
        );
    }

    public function test_vendor_with_countries_relation()
    {
        $relation = new Relation(
            ResourceType::VENDOR,
            'countries',
            ResourceType::COUNTRY
        );

        $this->assertCorrectRelationWithMany(
            $relation,
            'Vendors with unknown countries:',
            'country codes'
        );
    }
}
