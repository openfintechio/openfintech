<?php

namespace Oft\Generator\Service;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\MdTableColumnDto;
use Oft\Generator\Dto\PaymentMethodDto;
use Oft\Generator\Dto\PaymentServiceDto;
use Oft\Generator\Dto\ProviderDto;
use Oft\Generator\Enums\MdTableColumnAlignEnum;
use Oft\Generator\Enums\TextEmphasisPatternEnum;
use Oft\Generator\Md\MdLink;
use Oft\Generator\Md\MdTable;
use Oft\Generator\Traits\ImagesTrait;
use Oft\Generator\Md\MdCode;
use Oft\Generator\Md\MdCodeBlock;
use Oft\Generator\Md\MdHeader;
use Oft\Generator\Md\MdImage;
use Oft\Generator\Md\MdText;

final class PaymentMethodOverviewBuilder extends MdBuilder
{
    use ImagesTrait;

    /* @var PaymentMethodDto */
    private $data;

    public function __construct(DataProvider $dataProvider, PaymentMethodDto $data)
    {
        parent::__construct($dataProvider);
        $this->data = $data;
    }

    private function buildRelatedPaymentServices(): void
    {
        $services = array_filter($this->dataProvider->getPaymentServices(), function (PaymentServiceDto $serviceDto) {
            return null !== $serviceDto->method && $this->data->code === $serviceDto->method;
        });

        if (empty($services)) {
            return;
        }

        $this->add(new MdHeader('Payment Services', 2), true);
        $this->br();
        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), 'The list of '));
        $this->add(new MdLink("Payment Services", "/payment-services/"));
        $this->addString(" based on the ".(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::ITALIC), $this->data->getName()->en ?? ''))->toString(), true);

        $this->add(new MdTable($services, [
            MdTableColumnDto::fromArray([
                'key' => 'Icon',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentServiceDto $service) {
                    return new MdImage($this->getPaymentMethodIcon($service->method), $service->method);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentServiceDto $service) {
                    return new MdLink($service->code, '/payment-services/' . $service->code . '/');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (PaymentServiceDto $service) {
                    return new MdCode($service->code);
                },
            ]),
        ]), true);
    }

    private function buildRelatedPaymentProviders(): void
    {
        $providers = array_filter($this->dataProvider->getProviders(), function (ProviderDto $provider) {
            return null !== $provider->paymentMethod && in_array($this->data->code, $provider->paymentMethod);
        });

        if (empty($providers)) {
            return;
        }

        $this->add(new MdHeader('Payment Providers', 2), true);
        $this->br();
        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::PLAIN), 'The list of '));
        $this->add(new MdLink("Payment Providers", "/payment-providers/"));
        $this->addString(" that support the ".(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::ITALIC), $this->data->getName()->en ?? ''))->toString(), true);

        $this->add(new MdTable($providers, [
            MdTableColumnDto::fromArray([
                'key' => 'Icon',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (ProviderDto $provider) {
                    return new MdImage($this->getProviderIcon($provider->code), $provider->code);
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Name',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (ProviderDto $provider) {
                    return new MdLink($provider->getName()->en ?? '', '/payment-providers/' . $provider->code . '/');
                },
            ]),
            MdTableColumnDto::fromArray([
                'key' => 'Code',
                'align' => new MdTableColumnAlignEnum(MdTableColumnAlignEnum::CENTER),
                'set_template' => function (ProviderDto $provider) {
                    return new MdCode($provider->code);
                },
            ]),
        ]), true);
    }

    public function build(): void
    {
        $this->add(new MdHeader($this->data->getName()->en ?? '', 1), true);
        $this->add(new MdImage($this->getPaymentMethodLogo($this->data->code), $this->data->code), true);

        $this->add(new MdHeader('General', 2), true);
        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Code:'));
        $this->space();
        $this->add(new MdCode($this->data->code), true);
        $this->br();

        if (null !== $this->data->vendor) {
            $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Vendor:'));
            $this->space();
            $this->add(new MdCode($this->data->vendor));
            $this->space();
            $this->add(new MdLink('show -->', '/vendors/' . $this->data->vendor . '/'), true);
            $this->br();
        }

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

        $this->add(new MdText(new TextEmphasisPatternEnum(TextEmphasisPatternEnum::BOLD), 'Category:'));
        $this->space();
        $this->add(new MdCode($this->data->category), true);
        $this->br();

        if (null !== $this->data->countries) {
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
        $this->add(new MdImage($this->getPaymentMethodLogo($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getPaymentMethodLogo($this->data->code)), true);

        $this->add(new MdHeader('Icon', 3), true);
        $this->add(new MdImage($this->getPaymentMethodIcon($this->data->code), $this->data->code), true);
        $this->add(new MdCodeBlock($this->getPaymentMethodIcon($this->data->code)), true);

        $this->buildRelatedPaymentServices();
        $this->buildRelatedPaymentProviders();

        $this->add(new MdHeader('JSON Object', 2), true);
        $this->add(new MdCodeBlock(json_encode($this->data->toArray()), 'json'), true);
    }
}