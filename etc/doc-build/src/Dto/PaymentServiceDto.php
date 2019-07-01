<?php

namespace Oft\Generator\Dto;

final class PaymentServiceDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var string */
    public $flow;

    /** @var string */
    public $method;

    /** @var string */
    public $currency;

    /** @var array|null */
    public $fields;

    /** @var number|null */
    public $amountMin;

    /** @var number|null */
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