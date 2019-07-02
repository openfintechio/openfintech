<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\CategoryDto;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\PayoutMethodDto;
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

final class PayoutMethodsListBuilder extends MdBuilder
{
    use ImagesTrait, UtilsTrait;

    /* @var array */
    private $methods;

    /* @var array */
    private $categories;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->methods = $this->sort($this->dataProvider->getPayoutMethods());
        $this->categories = $this->sort($this->dataProvider->getPayoutMethodCategories());
    }

    private function buildMethodsTable(): void
    {
        $this->add(new MdHeader('Payout Methods', 1), true);

        $table = new MdTable($this->methods, [
            MdTableColumnDto::fromArray([
                'key' => 'Logo',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutMethodDto $row) {
                    return new MdImage($this->getPayoutMethodLogo($row->code), $row->code);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutMethodDto $row) {
                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $row->getName()->en ?? ''))->toString(),
                        $row->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutMethodDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]);

        $table->setRowSlot(function (PayoutMethodDto $row, array $data) {
            $index = $this->array_find_index($data, function (PayoutMethodDto $r) use ($row) {
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
        $this->add(new MdHeader('Payout Method Categories', 1), true);
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

    public function build(): void
    {
        $this->buildMethodsTable();
        $this->buildCategoriesTable();
    }
}