<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\PaymentMethodDto;
use Oft\Generator\Dto\PaymentServiceDto;
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

final class PaymentServicesListBuilder extends MdBuilder
{
    use ImagesTrait, UtilsTrait;

    /* @var array */
    private $data;

    public function __construct(DataProvider $dataProvider)
    {
        parent::__construct($dataProvider);
        $this->data = $this->sort($this->dataProvider->getPaymentServices());
    }

    public function build(): void
    {
        $this->add(new MdHeader('Payment Services', 1), true);

        $table = new MdTable($this->data, [
            MdTableColumnDto::fromArray([
                'key' => 'Method Logo',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentServiceDto $row) {
                    /* @var PaymentMethodDto $paymentMethod */
                    $paymentMethod = $this->array_find($this->dataProvider->getPaymentMethods(), function (PaymentMethodDto $pm) use ($row) {
                        return $pm->code === $row->method;
                    });

                    return new MdImage($this->getPaymentMethodLogo($paymentMethod->code),$paymentMethod->getName()->en ?? '');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Method Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentServiceDto $row) {
                    /* @var PaymentMethodDto $paymentMethod */
                    $paymentMethod = $this->array_find($this->dataProvider->getPaymentMethods(), function (PaymentMethodDto $pm) use ($row) {
                        return $pm->code === $row->method;
                    });

                    return new MdLink(
                        (new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $paymentMethod->getName()->en ?? ''))->toString(),
                        '/payment-methods/' . $paymentMethod->code . '/'
                    );
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentServiceDto $row) {
                    return new MdLink((new MdCode($row->code))->toString(), $row->code . '/');
                },
            ]),
        ]);

        $table->setRowSlot(function (PaymentServiceDto $paymentService, array $data) {

            $index = $this->array_find_index($data, function (PaymentServiceDto $r) use ($paymentService) {
                return $r->code === $paymentService->code;
            });
            $key = strtoupper($paymentService->code[0]);

            if ($index === 0 || strtoupper($data[$index - 1]->code[0]) !== $key) {
                return "||| **$key** ||\n";
            }
        });

        $this->add($table);
    }
}