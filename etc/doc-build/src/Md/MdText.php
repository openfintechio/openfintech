<?php

namespace Oft\Generator\Md;

use Oft\Generator\Enums\TextEmphasisPatternEnum;

class MdText implements MdElementInterface
{
    /* @var TextEmphasisPatternEnum */
    private $pattern;

    /* @var string */
    private $str;

    public function __construct(TextEmphasisPatternEnum $pattern, string $str)
    {
        $this->pattern = $pattern;
        $this->str = $str;
    }

    public function toString(): string
    {
        return strtr($this->pattern->getValue(), ['$str' => $this->str]);
    }
}