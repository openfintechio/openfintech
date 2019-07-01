<?php

namespace Oft\Generator\Enums;

abstract class AbstractEnum
{
    /* @var string */
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}