<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\CategoryDto;
use Oft\Generator\Dto\CurrencyDto;
use Oft\Generator\Dto\CurrencyTypeDto;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Enums\MdTableColumnAlignEnum;
use Oft\Generator\Enums\TextEmphasisPatternEnum;
use Oft\Generator\Md\MdCode;
use Oft\Generator\Md\MdHeader;
use Oft\Generator\Md\MdImage;
use Oft\Generator\Md\MdLink;
use Oft\Generator\Md\MdTable;
use Oft\Generator\Md\MdText;
use Oft\Generator\Traits\ImagesTrait;
use Oft\Generator\Traits\UtilsTrait;

final class CurrenciesListBuilder extends MdBuilder
{
    use UtilsTrait, ImagesTrait;

    /* @var array */
    private $currencies;

    /* @var array */
    private $types;

    /* @var array */
    private $categories;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->currencies = $this->sort($this->dataProvider->getCurrencies());
        $this->types = $this->sort($this->dataProvider->getCurrencyTypes());
        $this->categories = $this->sort($this->dataProvider->getCurrencyCategories());
    }

    private function buildCurrenciesTable(): void
    {
        $this->add(new MdHeader('Currencies', 1), true);

        $table = new MdTable($this->currencies, [
            MdTableColumnDto::fromArray([
                'key' => 'Icon',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CurrencyDto $row) {
                    return new MdImage($this->getCurrencyIcon($row->code), $row->code);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CurrencyDto $row) {
                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $row->getName()->en ?? ''))->toString(),
                        $row->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CurrencyDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]);

        $table->setRowSlot(function (CurrencyDto $row, array $data) {
            $index = $this->array_find_index($data, function (CurrencyDto $r) use ($row) {
                return $r->code === $row->code;
            });
            $key = strtoupper($row->code[0]);

            if ($index === 0 || strtoupper($data[$index - 1]->code[0]) !== $key) {
                return "|| **$key** ||\n";
            }
        });

        $this->add($table, true);

    }

    private function buildCategoriesTable(): void
    {
        $this->add(new MdHeader('Currency Categories', 1), true);
        $this->add(new MdTable($this->categories, [
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CategoryDto $row) {
                    return new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), $row->getName()->en ?? '');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CategoryDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]), true);

    }

    private function buildTypesTable(): void
    {
        $this->add(new MdHeader('Currency Types', 1), true);
        $this->add(new MdTable($this->types, [
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CurrencyTypeDto $row) {
                    return new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), $row->getName()->en ?? '');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (CurrencyTypeDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]), true);

    }

    public function build(): void
    {
        $this->buildCurrenciesTable();
        $this->buildCategoriesTable();
        $this->buildTypesTable();
    }
}