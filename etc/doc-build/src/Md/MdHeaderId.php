<?php

namespace Oft\Generator\Md;

class MdHeaderId implements MdElementInterface
{
    private const PATTERN = '{#$str} ';

    /* @var string */
    private $str;

    public function __construct(string $str)
    {
        $this->str = $str;
    }

    public function toString(): string
    {
        return strtr(self::PATTERN, ['$str' => $this->str]);
    }
}