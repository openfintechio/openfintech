<?php

namespace Oft\Generator\Dto;

final class PayoutServiceDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var string|null */
    public $method;

    /** @var string|null */
    public $currency;

    /** @var array|null */
    public $fields;

    /** @var number */
    public $amountMin;

    /** @var number */
    public $amountMax;

    public function getFields(): array
    {
        $val = [];

        foreach ($this->fields as $field) {
            array_push($val, ServiceFieldDto::fromArray($field));
        }

        return $val;
    }
}