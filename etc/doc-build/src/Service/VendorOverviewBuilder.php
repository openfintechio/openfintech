<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\VendorDto;
use Oft\Generator\Enums\TextEmphasisPatternEnum;
use Oft\Generator\Md\MdCode;
use Oft\Generator\Md\MdCodeBlock;
use Oft\Generator\Md\MdHeader;
use Oft\Generator\Md\MdImage;
use Oft\Generator\Md\MdLink;
use Oft\Generator\Md\MdText;
use Oft\Generator\Traits\ImagesTrait;

final class VendorOverviewBuilder extends MdBuilder
{
    use ImagesTrait;

    /* @var VendorDto */
    private $data;

    public function __construct(DataProvider $dataProvider, VendorDto $data)
    {
        parent::__construct($dataProvider);
        $this->data = $data;
    }

    public function build(): void
    {
        $this->add(new MdHeader($this->data->getName()->en ?? '', 1), true);
        $this->add(new MdImage($this->getVendorLogo($this->data->code), $this->data->code), true);

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

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Status:'));
        $this->space();
        $this->add(new MdCode($this->data->status), true);
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

        if (null !== $this->data->links) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Links:'));
            $this->br();

            foreach ($this->data->links as $where => $link) {
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), ": $where"));
                $this->space();
                $this->add(new MdLink($link, $link), true);
                $this->br();
            }
        }

        if (null !== $this->data->contacts) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Contacts:'));
            $this->br();

            foreach ($this->data->contacts as $where => $link) {
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), "$where: $link"));
            }
        }

        if (null !== $this->data->address) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Address:'));
            $this->br();

            foreach ($this->data->address as $key => $val) {
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), "$key: $val"), true);
            }
        }

        if (null !== $this->data->socialProfiles) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Social profiles:'));
            $this->br();

            foreach ($this->data->socialProfiles as $sp => $link) {
                $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), $sp));
                $this->space();
                $this->add(new MdLink($link, $link), true);
                $this->br();
            }
        }

        $this->add(new MdHeader('Images', 2), true);
        $this->add(new MdHeader('Logo', 3), true);
        $this->br();
        $this->add(new MdImage($this->getVendorLogo($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getVendorLogo($this->data->code)), true);

        $this->add(new MdHeader('Icon', 3), true);
        $this->br();
        $this->add(new MdImage($this->getVendorIcon($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getVendorIcon($this->data->code)), true);

        $this->add(new MdHeader('JSON Object', 2), true);
        $this->add(new MdCodeBlock(json_encode($this->data->toArray()), 'json'), true);
    }
}