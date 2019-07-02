<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\VendorDto;
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

final class VendorsListBuilder extends MdBuilder
{
    use UtilsTrait, ImagesTrait;

    /* @var array */
    private $data;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->data = $this->sort($this->dataProvider->getVendors());
    }

    public function build(): void
    {
        $this->add(new MdHeader('Vendors', 1), true);

        $table = new MdTable($this->data, [
            MdTableColumnDto::fromArray([
                'key' => 'Logo',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (VendorDto $row) {
                    return new MdImage($this->getVendorLogo($row->code, '600'), $row->code);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (VendorDto $row) {
                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $row->getName()->en ?? ''))->toString(),
                        $row->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (VendorDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]);

        $table->setRowSlot(function (VendorDto $row, array $data) {
            $index = $this->array_find_index($data, function (VendorDto $r) use ($row) {
                return $r->code === $row->code;
            });
            $key = strtoupper($row->code[0]);

            if ($index === 0 || strtoupper($data[$index - 1]->code[0]) !== $key) {
                return "|| **$key** ||\n";
            }
        });

        $this->add($table, true);
    }
}