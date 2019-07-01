<?php

namespace Oft\Generator\Dto;

final class PayoutMethodDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var string|null */
    public $vendor;

    /** @var array */
    public $name;

    /** @var array|null */
    public $description;

    /** @var array|null */
    public $countries;

    /** @var string */
    public $category;

    public function getName(): Translatable
    {
        return Translatable::fromArray($this->name);
    }

    public function getDescription(): Translatable
    {
        return Translatable::fromArray($this->description);
    }

}