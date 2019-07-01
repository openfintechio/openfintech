<?php

namespace Oft\Generator\Md;

class MdHeader implements MdElementInterface
{
    private const PATTERN = '#';

    /* @var int */
    private $level;

    /* @var string */
    private $str;

    public function __construct(string $string, int $level)
    {
        $this->str = $string;
        $this->level = $level;
    }

    public function toString(): string
    {
        return "\n".str_repeat(self::PATTERN, $this->level).' '.$this->str;
    }
}