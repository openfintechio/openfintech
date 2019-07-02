<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\PayoutMethodDto;
use Oft\Generator\Dto\PayoutServiceDto;
use Oft\Generator\Dto\ServiceFieldDto;
use Oft\Generator\Enums\MdTableColumnAlignEnum;
use Oft\Generator\Enums\TextEmphasisPatternEnum;
use Oft\Generator\Traits\ImagesTrait;
use Oft\Generator\Md\MdCode;
use Oft\Generator\Md\MdCodeBlock;
use Oft\Generator\Md\MdHeader;
use Oft\Generator\Md\MdImage;
use Oft\Generator\Md\MdLink;
use Oft\Generator\Md\MdTable;
use Oft\Generator\Md\MdText;
use Oft\Generator\Traits\UtilsTrait;

final class PayoutServiceOverviewBuilder extends MdBuilder
{
    use ImagesTrait, UtilsTrait;

    /* @var PayoutServiceDto */
    private $data;

    public function __construct(DataProvider $dataProvider, PayoutServiceDto $data)
    {
        parent::__construct($dataProvider);
        $this->data = $data;
    }

    private function getMethod(): PayoutMethodDto
    {
        return $this->array_find($this->dataProvider->getPayoutMethods(), function (PayoutMethodDto $pom) {
            return $pom->code === $this->data->method;
        });
    }

    private function buildField(ServiceFieldDto $field, int $index): void
    {
        $this->addString(((string) ($index + 1)).'. '.((new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), (new MdCode($field->key))->toString()))->toString()), true);
        $this->br();

        $this->addString('Type: ', false, 1);
        $this->add(new MdCode($field->type), true);
        $this->br();

        $this->addString('Regexp: ', false, 1);
        $this->add(new MdCode($field->regexp), true);
        $this->br();

        $this->addString('Required: ', false, 1);
        $this->add(new MdCode((string) $field->required), true);
        $this->br();

        $this->addString('Label: ', true, 1);
        foreach ($field->label as $lang => $val) {
            $viewLang = strtoupper($lang);
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ": [$viewLang] $val"), true, 1);
        }
        $this->br();

        $this->addString('Hint: ', true, 1);
        foreach ($field->hint as $lang => $val) {
            $viewLang = strtoupper($lang);
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ": [$viewLang] $val"), true,1);
        }
        $this->br();
    }

    public function build(): void
    {
        $this->add(new MdHeader(($this->getMethod()->getName()->en ?? '').' (service)', 1), true);
        $this->add(new MdImage($this->getPayoutMethodLogo($this->data->code), $this->data->code), true);

        $this->add(new MdHeader('General', 2),true);
        $this->br();
        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Code:'));
        $this->space();
        $this->add(new MdCode($this->data->code),true);
        $this->br();

        if (null !== $this->data->method) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Method:'));
            $this->space();
            $this->add(new MdCode($this->data->method));
            $this->space();
            $this->add(new MdLink('show -->', '/payout-methods/' . $this->data->method . '/'), true);
            $this->br();
        }

        if (null !== $this->data->currency) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Currency:'));
            $this->space();
            $this->add(new MdCode($this->data->currency));
            $this->space();
            $this->add(new MdLink('show -->', '/currencies/' . $this->data->currency . '/'), true);
            $this->br();
        }

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Name:'), true);
        $this->br();
        foreach ($this->getMethod()->getName() as $lang => $val) {
            if (null !== $val) {
                $viewLang = strtoupper($lang);
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ":\t[$viewLang] $val"), true);
            }
        }
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Amount limits:'));
        $this->space();
        $this->addString('from '.((new MdCode($this->data->amountMin))->toString()).' to '.((new MdCode($this->data->amountMax))->toString()),null === $this->data->currency);
        if (null !== $this->data->currency) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ' '.$this->data->currency));
        }
        $this->br();

        if (null !== $this->data->fields) {
            $this->add(new MdHeader('Fields', 2),true);
            $this->add(new MdHeader('Overview', 3),true);

            $this->add(new MdTable($this->data->getFields(), [
                MdTableColumnDto::fromArray([
                    'key' => 'Key',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (ServiceFieldDto $field) {
                        return new MdCode($field->key);
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Required',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (ServiceFieldDto $field) {
                        return new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), $field->required ? '✔' : '✗');
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Type',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (ServiceFieldDto $field) {
                        return new MdCode($field->type);
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Regexp',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (ServiceFieldDto $field) {
                        return new MdCode($field->regexp);
                    },
                ]),
            ]), true);

            $this->add(new MdHeader('Details', 3),true);
            $this->br();
            foreach ($this->data->getFields() as $index => $field) {
                $this->buildField($field, $index);
            }
        }

        $this->add(new MdHeader('JSON Object', 2), true);
        $this->add(new MdCodeBlock(json_encode($this->data->toArray()), 'json'), true);
    }
}