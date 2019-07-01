<?php

namespace Oft\Generator\Dto;

final class VendorDto extends BaseDto
{
    /** @var string */
    public $code;

    /** @var array */
    public $name;

    /** @var string */
    public $status;

    /** @var array|null */
    public $description;

    /** @var array|null */
    public $links;

    /** @var array|null */
    public $countries;

    /** @var array|null */
    public $contacts;

    /** @var array|null */
    public $address;

    /** @var array|null */
    public $socialProfiles;

    public function getName(): Translatable
    {
        return Translatable::fromArray($this->name);
    }
}