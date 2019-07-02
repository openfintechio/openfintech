<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\CurrencyDto;
use Oft\Generator\Enums\TextEmphasisPatternEnum;
use Oft\Generator\Md\MdCode;
use Oft\Generator\Md\MdCodeBlock;
use Oft\Generator\Md\MdHeader;
use Oft\Generator\Md\MdImage;
use Oft\Generator\Md\MdText;
use Oft\Generator\Traits\ImagesTrait;

final class CurrencyOverviewBuilder extends MdBuilder
{
    use ImagesTrait;

    /* @var CurrencyDto */
    private $data;

    public function __construct(DataProvider $dataProvider, CurrencyDto $data)
    {
        parent::__construct($dataProvider);
        $this->data = $data;
    }

    public function build(): void
    {
        $this->add(new MdHeader($this->data->getName()->en ?? '', 1), true);
        $this->add(new MdImage($this->getCurrencyIcon($this->data->code), $this->data->code), true);

        $this->add(new MdHeader('General', 2), true);
        $this->br();
        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Code:'));
        $this->space();
        $this->add(new MdCode($this->data->code), true);
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Name:'), true);
        $this->br();
        foreach ($this->data->name as $lang => $val) {
            $viewLang = strtoupper($lang);
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ":\t[$viewLang] $val"), true);
        }
        $this->br();

        if (null !== $this->data->symbol) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Symbol:'));
            $this->space();
            $this->add(new MdCode($this->data->symbol), true);
            $this->br();
        }

        if (null !== $this->data->nativeSymbol) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Native symbol:'));
            $this->space();
            $this->add(new MdCode($this->data->nativeSymbol), true);
            $this->br();
        }

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Category:'));
        $this->space();
        $this->add(new MdCode($this->data->category), true);
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Type:'));
        $this->space();
        $this->add(new MdCode($this->data->type), true);
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Exponent:'));
        $this->space();
        $this->add(new MdCode((string) $this->data->exponent), true);
        $this->br();

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Parent currency multiplier:'));
        $this->space();
        $this->add(new MdCode((string) $this->data->parentCurrencyMultiplier), true);
        $this->br();

        if (null !== $this->data->isoNumeric3Code) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'ISO numeric-3 code:'));
            $this->space();
            $this->add(new MdCode((string) $this->data->isoNumeric3Code), true);
            $this->br();
        }

        if (null !== $this->data->isoAlpha3Code) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'ISO alpha-3 code:'));
            $this->space();
            $this->add(new MdCode((string) $this->data->isoAlpha3Code), true);
            $this->br();
        }

        if (null !== $this->data->metadata) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Metadata:'), true);
            $this->br();

            foreach ($this->data->metadata as $key => $value) {
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ":\t$key:"));
                $this->space();
                $this->add(new MdCode($value), true);
                $this->br();
            }
        }

        $this->add(new MdHeader('Images', 2), true);
        $this->add(new MdHeader('Icon', 3), true);
        $this->br();
        $this->add(new MdImage($this->getCurrencyIcon($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getCurrencyIcon($this->data->code)), true);

        $this->add(new MdHeader('JSON Object', 2), true);
        $this->add(new MdCodeBlock(json_encode($this->data->toArray()), 'json'), true);
    }
}