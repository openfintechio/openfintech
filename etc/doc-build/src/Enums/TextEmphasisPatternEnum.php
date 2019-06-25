<?php

namespace Oft\Generator\Enums;

class TextEmphasisPatternEnum extends AbstractEnum
{
    public const PLAIN = '$str';
    public const BOLD = '**$str**';
    public const ITALIC = '_$str_';
    public const BOLD_AND_ITALIC = '***$str***';
}