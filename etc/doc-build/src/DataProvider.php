<?php

namespace Oft\Generator;

use Oft\Generator\Dto\CategoryDto;
use Oft\Generator\Dto\FlowDto;
use Oft\Generator\Dto\PaymentMethodDto;
use Oft\Generator\Dto\PaymentServiceDto;
use Oft\Generator\Dto\PayoutMethodDto;
use Oft\Generator\Dto\PayoutServiceDto;
use Oft\Generator\Dto\ProviderDto;

class DataProvider
{
    const PATH_TO_DATA = __DIR__.'/../../../data';
    const PROVIDERS_FILENAME = '/payment_providers.json';
    const PAYMENT_METHODS_FILENAME = '/payment_methods.json';
    const PAYOUT_SERVICES_FILENAME = '/payout_services.json';
    const PAYMENT_SERVICES_FILENAME = '/payment_services.json';
    const PAYOUT_METHODS_FILENAME = '/payout_methods.json';
    const PAYMENT_METHOD_CATEGORIES_FILENAME = '/payment_method_categories.json';
    const PAYMENT_FLOWS_FILENAME = '/payment_flows.json';

    /* @var array */
    private $providers;

    /* @var array */
    private $paymentMethods;

    /* @var array */
    private $payoutServices;

    /* @var array */
    private $paymentServices;

    /* @var array */
    private $payoutMethods;

    /* @var array */
    private $paymentMethodCategories;

    /* @var array */
    private $paymentFlows;

    public function __construct()
    {
        try {
            $this->setProviders($this->getJsonContent(self::PATH_TO_DATA.self::PROVIDERS_FILENAME));
            $this->setPaymentMethods($this->getJsonContent(self::PATH_TO_DATA.self::PAYMENT_METHODS_FILENAME));
            $this->setPayoutServices($this->getJsonContent(self::PATH_TO_DATA.self::PAYOUT_SERVICES_FILENAME));
            $this->setPaymentServices($this->getJsonContent(self::PATH_TO_DATA.self::PAYMENT_SERVICES_FILENAME));
            $this->setPayoutMethods($this->getJsonContent(self::PATH_TO_DATA.self::PAYOUT_METHODS_FILENAME));
            $this->setPaymentMethodCategories($this->getJsonContent(self::PATH_TO_DATA.self::PAYMENT_METHOD_CATEGORIES_FILENAME));
            $this->setPaymentFlows($this->getJsonContent(self::PATH_TO_DATA.self::PAYMENT_FLOWS_FILENAME));
        } catch (\Throwable $ex) {
            echo $ex->getMessage();
        }
    }

    private function getJsonContent($path): array
    {
        try {
            return json_decode(file_get_contents($path), true);
        } catch (\Throwable $ex) {
            throw $ex;
        }
    }

    private function setProviders(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, ProviderDto::fromArray($item));
        }

        $this->providers = $tmp;
    }

    private function setPaymentMethods(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, PaymentMethodDto::fromArray($item));
        }

        $this->paymentMethods = $tmp;
    }

    private function setPayoutServices(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, PayoutServiceDto::fromArray($item));
        }

        $this->payoutServices = $tmp;
    }

    private function setPaymentServices(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, PaymentServiceDto::fromArray($item));
        }

        $this->paymentServices = $tmp;
    }

    private function setPayoutMethods(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, PayoutMethodDto::fromArray($item));
        }

        $this->payoutMethods = $tmp;
    }

    private function setPaymentMethodCategories(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, CategoryDto::fromArray($item));
        }

        $this->paymentMethodCategories = $tmp;
    }

    private function setPaymentFlows(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, FlowDto::fromArray($item));
        }

        $this->paymentFlows = $tmp;
    }

    public function getProviders(): array
    {
        return $this->providers;
    }

    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }

    public function getPayoutServices(): array
    {
        return $this->payoutServices;
    }

    public function getPayoutMethods(): array
    {
        return $this->payoutMethods;
    }

    public function getPaymentServices(): array
    {
        return $this->paymentServices;
    }

    public function getPaymentMethodCategories(): array
    {
        return $this->paymentMethodCategories;
    }

    public function getPaymentFlows(): array
    {
        return $this->paymentFlows;
    }
}