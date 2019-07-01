<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Md\MdElementInterface;

class MdBuilder
{
    /** @var string */
    private $content = "";

    /* @var DataProvider */
    protected $dataProvider;

    public function __construct(DataProvider $dataProviderProvider)
    {
        $this->dataProvider = $dataProviderProvider;
    }

    public function br(): void
    {
        $this->content = $this->content." \n";
    }

    public function space(): void
    {
        $this->content = $this->content.' ';
    }

    public function add(MdElementInterface $element, bool $return = false, int $indent = 0): void
    {
        $this->content = $indent
            ? $this->content.str_repeat("\t", $indent).$element->toString()
            : $this->content.$element->toString();

        if ($return) {
            $this->br();
        }
    }

    public function addString(string $string, bool $return = false, int $indent = 0): void
    {
        $this->content = $indent
            ? $this->content.str_repeat("\t", $indent).$string
            : $this->content.$string;

        if ($return) {
            $this->br();
        }
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}