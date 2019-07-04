<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\CategoryDto;
use Oft\Generator\Dto\FlowDto;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\PaymentMethodDto;
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

final class PaymentMethodsListBuilder extends MdBuilder
{
    use ImagesTrait, UtilsTrait;

    /* @var array */
    private $methods;

    /* @var array */
    private $flows;

    /* @var array */
    private $categories;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->methods = $this->sort($this->dataProvider->getPaymentMethods());
        $this->flows = $this->sort($this->dataProvider->getPaymentFlows());
        $this->categories = $this->sort($this->dataProvider->getPaymentMethodCategories());
    }

    private function buildMethodsTable(): void
    {
        $this->add(new MdHeader('Payment Methods', 1), true);

        $table = new MdTable($this->methods, [
            MdTableColumnDto::fromArray([
                'key' => 'Logo',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentMethodDto $row) {
                    return new MdImage($this->getPaymentMethodLogo($row->code), $row->code);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentMethodDto $row) {
                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $row->getName()->en ?? ''))->toString(),
                        $row->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentMethodDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]);

        $table->setRowSlot(function (PaymentMethodDto $row, array $data) {
            $index = $this->array_find_index($data, function (PaymentMethodDto $r) use ($row) {
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
        $this->add(new MdHeader('Payment Method Categories', 1), true);
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

    private function buildFlowsTable(): void
    {
        $this->add(new MdHeader('Payment Flows', 1), true);
        $this->add(new MdTable($this->flows, [
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (FlowDto $row) {
                    return new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), $row->getName()->en ?? '');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (FlowDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]), true);
    }

    public function build(): void
    {
        $this->buildMethodsTable();
        $this->buildCategoriesTable();
        $this->buildFlowsTable();
    }
}