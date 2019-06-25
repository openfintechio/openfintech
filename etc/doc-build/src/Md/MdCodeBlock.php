<?php

namespace Oft\Generator\Md;

class MdCodeBlock implements MdElementInterface
{
    private const PATTERN = "\n".'```$highlight'."\n".'$str'."\n".'``` ';

    /* @var string */
    private $str;

    /* @var string */
    private $highlight;

    public function __construct(string $string, string $highlight = '')
    {
        $this->str = $string;
        $this->highlight = $highlight;
    }

    public function toString(): string
    {
        return strtr(self::PATTERN, [
            '$str' => $this->highlight === 'json' ? $this->formatJson($this->str) : $this->str,
            '$highlight' => $this->highlight
        ]);
    }

    private function formatJson(string $json): string
    {
        $result = '';
        $pos = 0;
        $strLen = strlen($json);
        $indentStr = '  ';
        $newLine = "\n";
        $prevChar = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {
            $char = substr($json, $i, 1);

            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $result .= $char;

            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        return $result;
    }
}