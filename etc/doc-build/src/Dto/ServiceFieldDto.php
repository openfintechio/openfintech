<?php

namespace Oft\Generator\Dto;

final class ServiceFieldDto extends BaseDto
{
    /** @var string */
    public $key;

    /** @var string */
    public $type;

    /** @var array */
    public $label;

    /** @var array */
    public $hint;

    /** @var string */
    public $regexp;

    /** @var bool */
    public $required;

    /** @var string|null */
    public $example;

    /** @var int */
    public $position;

    public function getLabel(): Translatable
    {
        return Translatable::fromArray($this->label);
    }

    public function getHint(): Translatable
    {
        return Translatable::fromArray($this->hint);
    }
}