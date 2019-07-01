<?php

namespace Oft\Generator\Dto;

class FlowDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var array */
    public $name;

    public function getName(): Translatable
    {
        return Translatable::fromArray($this->name);
    }
}