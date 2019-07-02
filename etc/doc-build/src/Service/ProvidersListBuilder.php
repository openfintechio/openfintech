<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\ProviderDto;
use Oft\Generator\Enums\MdTableColumnAlignEnum;
use Oft\Generator\Enums\TextEmphasisPatternEnum;
use Oft\Generator\Md\MdLink;
use Oft\Generator\Traits\ImagesTrait;
use Oft\Generator\Md\MdCode;
use Oft\Generator\Md\MdHeader;
use Oft\Generator\Md\MdImage;
use Oft\Generator\Md\MdTable;
use Oft\Generator\Md\MdText;
use Oft\Generator\Traits\UtilsTrait;

final class ProvidersListBuilder extends MdBuilder
{
    use ImagesTrait, UtilsTrait;

    /* @var array */
    private $data;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->data = $this->sort($this->dataProvider->getProviders());
    }

    public function build(): void
    {
        $this->add(new MdHeader('Payment Providers', 1), true);

        $providersTable = new MdTable($this->data, [
            MdTableColumnDto::fromArray([
                'key' => 'Logo',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (ProviderDto $row) {
                    return new MdImage($this->getProviderLogo($row->code, '600'), $row->code);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (ProviderDto $row) {
                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $row->getName()->en ?? ''))->toString(),
                        $row->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (ProviderDto $row) {
                    return new MdCode($row->code);
                },
            ]),
        ]);

        $providersTable->setRowSlot(function (ProviderDto $row, array $data) {
            $index = $this->array_find_index($data, function (ProviderDto $r) use ($row) {
               return $r->code === $row->code;
            });
            $key = strtoupper($row->code[0]);

            if ($index === 0 || strtoupper($data[$index - 1]->code[0]) !== $key) {
                return "|| **$key** ||\n";
            }
        });

        $this->add($providersTable, true);
    }
}