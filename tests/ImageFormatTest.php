<?php

declare(strict_types=1);

namespace Oft\Provider\tests;

use Oft\Generator\Enums\ResourceType;
use Tests\AbstractDataTest;
use Tests\Relation;

final class ImageFormatTest extends AbstractDataTest
{
    public function test_image_format(): void
    {
        $this->assertImageHasCorrectFormat(
            'Image must have only png or svg format:'
        );
    }
}
