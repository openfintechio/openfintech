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
        $this->data = $this->group($this->dataProvider->getPayoutServices());
    }

    private function group(array $data): array
    {
        $grouped = [];
        $by_method = [];

        /* @var PayoutServiceDto $payoutService */
        foreach ($this->sort($data, 'method') as $payoutService) {
            $method = $payoutService->method;

            if (array_key_exists($method, $by_method)) {
                array_push($by_method[$method], $payoutService);
            } else {
                $by_method[$method] = [$payoutService];
            }
        }

        foreach ($by_method as $method_code => $group) {
            $key = strtoupper($method_code[0]);

            if (array_key_exists($key, $grouped)) {
                $grouped[$key][$method_code] = $group;
            } else {
                $grouped[$key] = [$method_code => $group];
            }
        }

        return $grouped;
    }

    public function build(): void
    {
        $this->add(new MdHeader('Payout Services', 1), true);

        foreach ($this->data as $h => $group) {
            $this->add(new MdHeader((string) $h, 2), true);

            foreach ($group as $method_code => $methods) {
                $table = new MdTable($methods, [
                    MdTableColumnDto::fromArray([
                        'key' => 'Code',
                        'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                        'set_template' => function (PayoutServiceDto $row) {
                            return new MdLink((new MdCode($row->code))->toString(), $row->code.'/index.md');
                        },
                    ]),
                    MdTableColumnDto::fromArray([
                        'key' => 'Currency',
                        'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                        'set_template' => function (PayoutServiceDto $row) {
                            return new MdCode($row->currency ?? '');
                        },
                    ]),
                    MdTableColumnDto::fromArray([
                        'key' => 'Fields',
                        'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                        'set_template' => function (PayoutServiceDto $row) {
                            return new MdCode((string) count($row->fields ?? []));
                        },
                    ]),
                ]);

                /* @var PayoutMethodDto $payoutMethod */
                $payoutMethod = $this->array_find($this->dataProvider->getPayoutMethods(), function (PayoutMethodDto $pom) use ($method_code) {
                    return $pom->code === $method_code;
                });

                $image = (new MdImage($this->getPayoutMethodLogo($method_code),$payoutMethod->getName()->en ?? ''))->toString();
                /*
                 *  FIXME: Add link to payout method
                 * */
                $link = (new MdLink((new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), $payoutMethod->getName()->en ?? ''))->toString(), '#'))->toString();

                $table->setAppend("|$image $link| | | |");
                $this->add($table);
            }
        }
    }
}