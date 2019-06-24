<?php

namespace Oft\Generator;

use Oft\Generator\Dto\PaymentMethodDto;
use Oft\Generator\Dto\PayoutServiceDto;
use Oft\Generator\Dto\ProviderDto;
use Oft\Generator\Service\PaymentMethodOverviewBuilder;
use Oft\Generator\Service\PayoutServiceOverviewBuilder;
use Oft\Generator\Service\PayoutServicesListBuilder;
use Oft\Generator\Service\ProviderOverviewBuilder;
use Oft\Generator\Service\ProvidersListBuilder;
use Oft\Generator\Traits\UtilsTrait;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class DocBuilder
{
    use UtilsTrait;

    /* @var DataProvider */
    private $dataProvider;

    /* @var Filesystem */
    private $fs;

    /* @var string */
    private $outputPath;

    public function __construct(string $outputPath)
    {
        $this->fs = new Filesystem();
        $this->dataProvider = new DataProvider($outputPath);
        $this->outputPath = $outputPath;
    }

    private function pathToDocs(): string
    {
        return $this->outputPath . '/docs';
    }

    private function pathToConfig(): string
    {
        return $this->outputPath . '/mkdocs.yml';
    }

    private function writeFile(string $path, string $content): void
    {
        try {
            $this->fs->touch($path);
            $this->fs->chmod($path, 0777);
            $this->fs->dumpFile($path, $content);
        } catch (IOExceptionInterface $exception) {
            echo $exception->getMessage()."\n";
        }
    }

    private function createDirectory(string $path): void
    {
        try {
            $this->fs->mkdir($path, 0777);
        } catch (IOExceptionInterface $exception) {
            echo $exception->getMessage()."\n";
        }
    }

    private function buildProviders(): void
    {
        $this->createDirectory($this->pathToDocs().'/payment-providers');

        /* @var ProviderDto $provider */
        foreach ($this->sort($this->dataProvider->getProviders()) as $provider) {
            $providerOverviewBuilder = new ProviderOverviewBuilder($this->dataProvider, $provider);
            $providerOverviewBuilder->build();

            $this->createDirectory($this->pathToDocs().'/payment-providers/'.$provider->code);
            $this->writeFile($this->pathToDocs().'/payment-providers/'.$provider->code.'/index.md', $providerOverviewBuilder->getContent());
        }

        $providersListBuilder = new ProvidersListBuilder($this->dataProvider);
        $providersListBuilder->build();
        $this->writeFile($this->pathToDocs().'/payment-providers/index.md', $providersListBuilder->getContent());
    }

    private function buildPayoutServices(): void
    {
        $this->createDirectory($this->pathToDocs().'/payout-services');

        /* @var PayoutServiceDto $payoutService */
        foreach ($this->sort($this->dataProvider->getPayoutServices()) as $payoutService) {
            $payoutServiceOverviewBuilder = new PayoutServiceOverviewBuilder($this->dataProvider, $payoutService);
            $payoutServiceOverviewBuilder->build();

            $this->createDirectory($this->pathToDocs().'/payout-services/'.$payoutService->code);
            $this->writeFile($this->pathToDocs().'/payout-services/'.$payoutService->code.'/index.md', $payoutServiceOverviewBuilder->getContent());
        }

        $payoutServicesListBuilder = new PayoutServicesListBuilder($this->dataProvider);
        $payoutServicesListBuilder->build();
        $this->writeFile($this->pathToDocs().'/payout-services/index.md', $payoutServicesListBuilder->getContent());
    }

    private function buildPaymentMethods(): void
    {
        $this->createDirectory($this->pathToDocs().'/payment-methods');

        /* @var PaymentMethodDto $method */
        foreach ($this->dataProvider->getPaymentMethods() as $method) {
            $paymentMethodOverviewBuilder = new PaymentMethodOverviewBuilder($this->dataProvider, $method);
            $paymentMethodOverviewBuilder->build();

            $this->createDirectory($this->pathToDocs().'/payment-methods/'.$method->code);
            $this->writeFile($this->pathToDocs().'/payment-methods/'.$method->code.'/index.md', $paymentMethodOverviewBuilder->getContent());
        }
    }

    private function buildConfig(): void
    {
        $nav = [];

        $providers = [['Overview' => 'payment-providers/index.md']];
        foreach ($this->sort($this->dataProvider->getProviders()) as $provider) {
            array_push($providers, [ucfirst($provider->code) => 'payment-providers/'.$provider->code.'/index.md']);
        }
        array_push($nav, ['Providers' => $providers]);

        $payoutServices = [['Overview' => 'payout-services/index.md']];
        foreach ($this->sort($this->dataProvider->getPayoutServices()) as $service) {
            array_push($payoutServices, [ucfirst($service->code) => 'payout-services/'.$service->code.'/index.md']);
        }
        array_push($nav, ['Payout services' => $payoutServices]);

        $paymentMethods = [];
        foreach ($this->sort($this->dataProvider->getPaymentMethods()) as $method) {
            array_push( $paymentMethods, [ucfirst($method->code) => 'payment-methods/'.$method->code.'/index.md']);
        }
        array_push($nav, ['Payment methods' => $paymentMethods]);

        $this->writeFile($this->pathToConfig(), $this->dataProvider->getConfig().Yaml::dump(['nav' => $nav], 5, 2));
    }

    public function build(): void
    {
        $this->buildProviders();
        $this->buildPayoutServices();
        $this->buildPaymentMethods();
        $this->buildConfig();
    }
}