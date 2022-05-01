<?php

declare(strict_types=1);

namespace Oft\Provider\tests\Payment;

use Oft\Generator\Enums\ResourceType;
use Tests\AbstractDataTest;

final class FlowTest extends AbstractDataTest
{
    public function test_check_flows_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYMENT_FLOW,
            'Duplicate payment flows:'
        );
    }
}