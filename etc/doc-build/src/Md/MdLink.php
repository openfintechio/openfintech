<?php

namespace Oft\Generator\Md;

class MdLink implements MdElementInterface
{
    private const PATTERN = '[$str]($link)';

    /* @var string */
    private $str;

    /* @var string */
    private $link;

    public function __construct(string $str, string $link)
    {
        $this->str = $str;
        $this->link = $link;
    }

    public function toString(): string
    {
        return strtr(self::PATTERN, ['$str' => $this->str, '$link' => $this->link]);
    }
}