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
        $this->data = $this->groupProviders($this->dataProvider->getProviders());
    }

    private function groupProviders(array $data): array
    {
        $grouped = [];

        /* @var ProviderDto $provider */
        foreach ($this->sort($data) as $provider) {
            $key = strtoupper($provider->code[0]);

            if (array_key_exists($key, $grouped)) {
                array_push($grouped[$key], $provider);
            } else {
                $grouped[$key] = [$provider];
            }
        }

        return $grouped;
    }

    public function build(): void
    {
        $this->add(new MdHeader('Payment providers', 1), true);

        foreach ($this->data as $h => $group) {
            $this->add(new MdHeader((string) $h, 2), true);
            $this->add(new MdTable($group, [
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
                            $row->code.'/index.md'
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
            ]), true);
        }
    }
}