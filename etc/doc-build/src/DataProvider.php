<?php

namespace Oft\Generator;

use Oft\Generator\Dto\CategoryDto;
use Oft\Generator\Dto\CountryDto;
use Oft\Generator\Dto\CurrencyDto;
use Oft\Generator\Dto\CurrencyTypeDto;
use Oft\Generator\Dto\FlowDto;
use Oft\Generator\Dto\PaymentMethodDto;
use Oft\Generator\Dto\PaymentServiceDto;
use Oft\Generator\Dto\PayoutMethodDto;
use Oft\Generator\Dto\PayoutServiceDto;
use Oft\Generator\Dto\ProviderDto;
use Oft\Generator\Dto\VendorDto;

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
    const CURRENCIES_FILENAME = '/currencies.json';
    const CURRENCY_TYPES_FILENAME = '/currency_types.json';
    const CURRENCY_CATEGORIES_FILENAME = '/currency_categories.json';
    const VENDORS_FILENAME = '/vendors.json';
    const PAYOUT_METHOD_CATEGORIES_FILENAME = '/payout_method_categories.json';
    const COUNTRIES_FILENAME = '/countries.json';

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
    private $payoutMethodCategories;

    /* @var array */
    private $paymentFlows;

    /* @var array */
    private $currencies;

    /* @var array */
    private $currencyTypes;

    /* @var array */
    private $currencyCategories;

    /* @var array */
    private $vendors;

    /* @var array */
    private $countries;

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
            $this->setCurrencies($this->getJsonContent(self::PATH_TO_DATA.self::CURRENCIES_FILENAME));
            $this->setCurrencyTypes($this->getJsonContent(self::PATH_TO_DATA.self::CURRENCY_TYPES_FILENAME));
            $this->setCurrencyCategories($this->getJsonContent(self::PATH_TO_DATA.self::CURRENCY_CATEGORIES_FILENAME));
            $this->setVendors($this->getJsonContent(self::PATH_TO_DATA.self::VENDORS_FILENAME));
            $this->setPayoutMethodCategories($this->getJsonContent(self::PATH_TO_DATA.self::PAYOUT_METHOD_CATEGORIES_FILENAME));
            $this->setCountries($this->getJsonContent(self::PATH_TO_DATA.self::COUNTRIES_FILENAME));
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

    private function setCurrencies(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, CurrencyDto::fromArray($item));
        }

        $this->currencies = $tmp;
    }

    private function setCurrencyTypes(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, CurrencyTypeDto::fromArray($item));
        }

        $this->currencyTypes = $tmp;
    }

    private function setCurrencyCategories(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, CategoryDto::fromArray($item));
        }

        $this->currencyCategories = $tmp;
    }

    private function setVendors(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, VendorDto::fromArray($item));
        }

        $this->vendors = $tmp;
    }

    private function setPayoutMethodCategories(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, CategoryDto::fromArray($item));
        }

        $this->payoutMethodCategories = $tmp;
    }

    private function setCountries(array $data): void
    {
        $tmp = [];

        foreach ($data as $item) {
            array_push($tmp, CountryDto::fromArray($item));
        }

        $this->countries = $tmp;
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

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    public function getCurrencyTypes(): array
    {
        return $this->currencyTypes;
    }

    public function getCurrencyCategories(): array
    {
        return $this->currencyCategories;
    }

    public function getVendors(): array
    {
        return $this->vendors;
    }

    public function getPayoutMethodCategories(): array
    {
        return $this->payoutMethodCategories;
    }

    public function getCountries(): array
    {
        return $this->countries;
    }
}