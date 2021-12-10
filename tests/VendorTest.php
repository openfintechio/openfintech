<?php

declare(strict_types=1);

namespace Tests;

use Oft\Generator\Enums\ResourceType;

final class VendorTest extends AbstractDataTest
{
    public function test_check_vendors_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::VENDOR,
            'Duplicate vendors:'
        );
    }
}
