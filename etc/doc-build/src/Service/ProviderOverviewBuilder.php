<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\ProviderDto;
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

final class ProviderOverviewBuilder extends MdBuilder
{
    use ImagesTrait;

    /* @var ProviderDto */
    private $data;

    public function __construct(DataProvider $dataProvider, ProviderDto $data)
    {
        parent::__construct($dataProvider);
        $this->data = $data;
    }

    public function build(): void
    {
        $this->add(new MdHeader($this->data->getName()->en ?? '', 1), true);
        $this->add(new MdImage($this->getProviderLogo($this->data->code), $this->data->code), true);

        $this->add(new MdHeader('General', 2), true);
        $this->br();
        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Code:'));
        $this->space();
        $this->add(new MdCode($this->data->code), true);
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Vendor:'));
        $this->space();
        $this->add(new MdCode($this->data->vendor));
        $this->space();
        $this->add(new MdLink('show -->', '/vendors/' . $this->data->vendor . '/'), true);
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Name:'), true);
        $this->br();
        foreach ($this->data->name as $lang => $val) {
            $viewLang = strtoupper($lang);
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ":\t[$viewLang] $val"), true);
        }
        $this->br();

        if (null !== $this->data->description) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Description:'), true);
            $this->br();

            foreach ($this->data->description as $lang => $val) {
                $viewLang = strtoupper($lang);
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ": [$viewLang] $val"), true);
            }
            $this->br();
        }

        if (null !== $this->data->categories) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Categories:'));

            foreach ($this->data->categories as $category) {
                $this->add(new MdCode($category));
                if ($category !== end($this->data->categories)) {
                    $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), " ,"));
                }
            }

            $this->br();
            $this->br();
        }

        if (null !== $this->data->countries) {
            $this->br();
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Countries:'), true);
            $this->br();
            $this->addString(":");

            foreach ($this->data->countries as $code) {
                $this->addString("\t");
                $this->add(new MdImage($this->getFlagIcon(strtolower($code)), $code));
            }

            $this->br();
        }

        $this->add(new MdHeader('Images', 2), true);
        $this->add(new MdHeader('Logo', 3), true);
        $this->br();
        $this->add(new MdImage($this->getProviderLogo($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getProviderLogo($this->data->code)), true);

        $this->add(new MdHeader('Icon', 3), true);
        $this->br();
        $this->add(new MdImage($this->getProviderIcon($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getProviderIcon($this->data->code)), true);

        if (null !== $this->data->paymentMethod) {
            $this->add(new MdHeader('Payment Methods', 2), true);
            $this->br();
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), 'The list of supported '));
            $this->add(new MdLink("Payment Methods", "/payment-methods/"), true);
            $this->add(new MdTable($this->data->paymentMethod, [
                MdTableColumnDto::fromArray([
                    'key' => 'Icon',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (string $code) {
                        return new MdImage($this->getPaymentMethodIcon($code), $code);
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Name',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (string $code) {
                        return new MdLink($code, '/payment-methods/' . $code . '/');
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Code',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (string $code) {
                        return new MdCode($code);
                    },
                ]),
            ]), true);
        }

        if (null !== $this->data->payoutMethod) {
            $this->add(new MdHeader('Payout Methods', 2), true);
            $this->br();
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), 'The list of supported '));
            $this->add(new MdLink("Payout Methods", "/payout-methods/"), true);
            $this->add(new MdTable($this->data->payoutMethod, [
                MdTableColumnDto::fromArray([
                    'key' => 'Icon',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (string $code) {
                        return new MdImage($this->getPayoutMethodIcon($code), $code);
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Name',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (string $code) {
                        return new MdLink($code, 'payout-methods' . $code . '/');
                    },
                ]),
                MdTableColumnDto::fromArray([
                    'key' => 'Code',
                    'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                    'set_template' => function (string $code) {
                        return new MdCode($code);
                    },
                ]),
            ]), true);
        }

        $this->add(new MdHeader('JSON Object', 2), true);
        $this->add(new MdCodeBlock(json_encode($this->data->toArray()), 'json'), true);
    }
}