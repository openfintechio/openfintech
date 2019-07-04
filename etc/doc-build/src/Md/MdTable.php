<?php

namespace Oft\Generator\Md;

use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\BaseDto;

class MdTable implements MdElementInterface
{
    /* @var array */
    private $data;

    /* @var array */
    private $cols;

    /* @var string */
    private $table = "\n";

    /* @var string|null */
    private $append;

    /* @var callable|null */
    private $rowSlot;

    public function __construct(array $data, array $cols)
    {
        $this->data = $data;
        $this->cols = $cols;
    }

    private function br(): void
    {
        $this->table = $this->table." \n";
    }

    private function add(string $str): void
    {
        $this->table = $this->table.$str;
    }

    private function separator(): void
    {
        if (substr($this->table, -1) === "|") {
            return;
        }

        $this->table = $this->table."|";
    }

    private function addHeader(): void
    {
        /* @var MdTableColumnDto $col */
        foreach ($this->cols as $col) {
            $this->separator();
            $this->add($col->key);
            $this->separator();
        }

        $this->br();

        /* @var MdTableColumnDto $col */
        foreach ($this->cols as $col) {
            $this->separator();
            $this->add($col->align->getValue());
            $this->separator();
        }

        $this->br();
    }

    private function addRow($row): void
    {
        /* @var MdTableColumnDto $col */
        foreach ($this->cols as $col) {
            $this->separator();

            /* @var MdElementInterface $element */
            $element = call_user_func($col->setTemplate, $row);

            $this->add(str_replace('|', '\|', $element->toString()));
            $this->separator();
        }

        $this->br();
    }

    private function appendSlot(): void
    {
        if (!$this->append) {
            return;
        }

        $this->add("$this->append\n");
    }

    private function build(): void
    {
        $this->addHeader();
        $this->appendSlot();

        /* @var BaseDto $item */
        foreach ($this->data as $item) {
            if ($this->rowSlot) {
                $this->add(call_user_func($this->rowSlot, $item, $this->data) ?? '');
            }

            $this->addRow($item);
        }
    }

    public function setAppend(string $append): void
    {
        $this->append = $append;
    }

    public function setRowSlot(callable $rowSlot): void
    {
        $this->rowSlot = $rowSlot;
    }

    public function toString(): string
    {
        $this->build();

        return $this->table;
    }
}