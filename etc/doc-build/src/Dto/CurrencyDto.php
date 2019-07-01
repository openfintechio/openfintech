<?php

namespace Oft\Generator\Dto;

final class CurrencyDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var array */
    public $name;

    /** @var string */
    public $type;

    /** @var integer */
    public $exponent;

    /** @var integer */
    public $parentCurrencyMultiplier;

    /** @var string */
    public $category;

    /** @var string */
    public $isoNumeric3Code;

    /** @var string */
    public $isoAlpha3Code;

    /** @var string */
    public $symbol;

    /** @var string */
    public $nativeSymbol;

    /** @var string */
    public $metadata;

    public function getName(): Translatable
    {
        return Translatable::fromArray($this->name);
    }

}