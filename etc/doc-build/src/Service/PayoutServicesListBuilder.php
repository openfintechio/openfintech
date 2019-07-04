<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\PayoutMethodDto;
use Oft\Generator\Dto\PayoutServiceDto;
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

final class PayoutServicesListBuilder extends MdBuilder
{
    use ImagesTrait, UtilsTrait;

    /* @var array */
    private $data;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->data = $this->sort($this->dataProvider->getPayoutServices());
    }

    public function build(): void
    {
        $this->add(new MdHeader('Payout Services', 1), true);

        $table = new MdTable($this->data, [
            MdTableColumnDto::fromArray([
                'key' => 'Method Logo',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutServiceDto $row) {
                    $payoutMethod = $this->array_find($this->dataProvider->getPayoutMethods(), function (PayoutMethodDto $pom) use ($row) {
                        return $pom->code === $row->method;
                    });

                    return new MdImage($this->getPayoutMethodLogo($payoutMethod->code),$payoutMethod->getName()->en ?? '');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Method Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutServiceDto $row) {
                    $payoutMethod = $this->array_find($this->dataProvider->getPayoutMethods(), function (PayoutMethodDto $pom) use ($row) {
                        return $pom->code === $row->method;
                    });

                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $payoutMethod->getName()->en ?? ''))->toString(),
                        '/payout-methods/' . $payoutMethod->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutServiceDto $row) {
                    return new MdLink((new MdCode($row->code))->toString(), $row->code . '/');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Currency',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PayoutServiceDto $row) {
                    return new MdCode($row->currency ?? '');
                },
            ]),
        ]);

        $table->setRowSlot(function (PayoutServiceDto $payoutService, array $data) {
            $index = $this->array_find_index($data, function (PayoutServiceDto $r) use ($payoutService) {
                return $r->code === $payoutService->code;
            });
            $key = strtoupper($payoutService->code[0]);

            if ($index === 0 || strtoupper($data[$index - 1]->code[0]) !== $key) {
                return "||| **$key** ||\n";
            }
        });

        $this->add($table);
    }
}