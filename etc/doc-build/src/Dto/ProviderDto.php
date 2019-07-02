<?php

namespace Oft\Generator\Dto;

final class ProviderDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var array|null */
    public $description;

    /** @var string */
    public $vendor;

    /** @var array|null */
    public $categories;

    /** @var array|null */
    public $countries;

    /** @var array|null */
    public $paymentMethod;

    /** @var array|null */
    public $payoutMethod;

    /** @var array|null */
    public $metadata;

    /** @var array */
    public $name;

    public function getName(): Translatable
    {
        return Translatable::fromArray($this->name);
    }

    public function getDescription(): Translatable
    {
        return Translatable::fromArray($this->description);
    }
}