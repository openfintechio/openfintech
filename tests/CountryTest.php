<?php

declare(strict_types=1);

namespace Tests;

use Oft\Generator\Enums\ResourceType;

final class CountryTest extends AbstractDataTest
{
    public function test_check_countries_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::COUNTRY,
            'Duplicate countries:'
        );
    }
}
