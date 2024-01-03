<?php

declare(strict_types=1);

namespace Tests;

use Oft\Generator\DataProvider;
use Oft\Generator\Dto\BaseDto;
use Oft\Generator\Enums\ResourceType;
use PHPUnit\Framework\TestCase;

abstract class AbstractDataTest extends TestCase
{
    protected const ERROR_MISSING_LINE_TEMPLATE = "\t\"%s\" | %s:";
    protected const NOT_EXISTENT_ERROR_HEADER_TEMPLATE = "Non existent %s:";
    protected const ALLOWED_IMAGE_FORMAT = ['svg', 'png'];
    protected const MARKDOWN_FORMAT = 'md';
    protected const RESOURCES_PATH = __DIR__ . '/../resources';

    protected DataProvider $dataProvider;

    public function __construct()
    {
        $this->dataProvider = new DataProvider();
        parent::__construct();
    }

    protected function assertCorrectRelationWithOne(Relation $relation, string $errorHeader, string $eachElementComment): void
    {
        $mainResourceDtos = $this->getResourcesByType($relation->mainResourceType);
        $relatedResourceDtos = $this->getResourcesByType($relation->relatedResourceType);
        $codesOfRelatedResources = \array_map(
            static fn ($item) => $item->{$relation->identityParamNameOfRelated},
            $relatedResourceDtos
        );
        $missing = [];

        foreach ($mainResourceDtos as $mainResourceDto) {
            $identityOfRelatedInMain = $mainResourceDto->{$relation->identityParamNameOfRelatedInMain} ?? null;

            if (null === $identityOfRelatedInMain) {
                continue;
            }

            if (!\in_array($identityOfRelatedInMain, $codesOfRelatedResources, true)) {
                $missing[$identityOfRelatedInMain][] = $mainResourceDto->{$relation->identityParamNameOfMain};
            }
        }

        if (0 !== \count($missing)) {
            $this->fail($this->buildNotExistsMessage($missing, $errorHeader, $eachElementComment));
        }

        self::assertTrue(true);
    }

    public function assertCorrectRelationWithMany(
        Relation $relation,
        string $errorHeader,
        string $eachElementComment
    ): void {
        $mainResourceDtos = $this->getResourcesByType($relation->mainResourceType);
        $relatedResourceDtos = $this->getResourcesByType($relation->relatedResourceType);
        $codesOfRelatedResources = \array_map(
            static fn ($item) => $item->{$relation->identityParamNameOfRelated},
            $relatedResourceDtos
        );
        $missing = [];

        foreach ($mainResourceDtos as $mainResourceDto) {
            $relatedElements = $mainResourceDto->{$relation->identityParamNameOfRelatedInMain} ?? null;

            if (!\is_array($relatedElements)) {
                continue;
            }

            $diff = \array_diff($relatedElements, $codesOfRelatedResources);

            if (0 === \count($diff)) {
                continue;
            }

            $missing[$mainResourceDto->{$relation->identityParamNameOfMain}] = $diff;
        }

        if (0 !== \count($missing)) {
            $this->fail($this->buildNotExistsMessage(
                $missing,
                $errorHeader,
                $eachElementComment
            ));
        }

        self::assertTrue(true);
    }

    /**
     * @template T
     *
     * @param class-string<T> $resourceDtoClass
     */
    protected function assertResourceHasNoDuplication(
        string $resourceDtoClass,
        string $errorHeader,
        string $identityParamName = 'code'
    ): void {
        $resourceDtos = $this->getResourcesByType($resourceDtoClass);
        $resourceCodes = \array_map(static fn ($resourceDto) => $resourceDto->{$identityParamName}, $resourceDtos);

        $duplicates = \array_unique(\array_diff_assoc($resourceCodes, \array_unique($resourceCodes)));

        if (0 !== \count($duplicates)) {
            $message =  $errorHeader . PHP_EOL;
            $message .= "\t- " . \implode(PHP_EOL . "\t- ", $duplicates);

            $this->fail($message);
        }

        self::assertTrue(true);
    }

    protected function assertImageHasCorrectFormat(
        string $errorHeader
    ): void {
        $errors = $this->checkImageFormat();

        if (0 !== \count($errors)) {
            $message =  $errorHeader . PHP_EOL;
            $message .= "\t- " . \implode(PHP_EOL . "\t- ", $errors);

            $this->fail($message);
        }

        self::assertTrue(true);
    }

    protected function buildNotExistsMessage(array $missing, string $header, string $eachElementComment): string
    {
        $message = $header . PHP_EOL;

        foreach ($missing as $item => $places) {
            $message .= \sprintf(self::ERROR_MISSING_LINE_TEMPLATE, $item, $eachElementComment) . PHP_EOL;
            $message .= "\t\t- " . \implode("\n\t\t- ", $places);
            $message .= PHP_EOL;
        }

        return $message;
    }

    /**
     * @template T of BaseDto
     *
     * @param class-string<T> $dtoClass
     *
     * @return T[]
     */
    private function getResourcesByType(string $dtoClass): array
    {
        switch ($dtoClass) {
            case ResourceType::PAYOUT_SERVICE:
                return $this->dataProvider->getPayoutServices();
            case ResourceType::PAYOUT_METHOD:
                return $this->dataProvider->getPayoutMethods();
            case ResourceType::PAYMENT_SERVICE:
                return $this->dataProvider->getPaymentServices();
            case ResourceType::PAYMENT_METHOD:
                return $this->dataProvider->getPaymentMethods();
            case ResourceType::CURRENCY:
                return $this->dataProvider->getCurrencies();
            case ResourceType::PAYMENT_FLOW:
                return $this->dataProvider->getPaymentFlows();
            case ResourceType::CURRENCY_TYPE:
                return $this->dataProvider->getCurrencyTypes();
            case ResourceType::CURRENCY_CATEGORY:
                return $this->dataProvider->getCurrencyCategories();
            case ResourceType::PAYOUT_METHOD_CATEGORY:
                return $this->dataProvider->getPayoutMethodCategories();
            case ResourceType::PAYMENT_METHOD_CATEGORY:
                return $this->dataProvider->getPaymentMethodCategories();
            case ResourceType::VENDOR:
                return $this->dataProvider->getVendors();
            case ResourceType::PAYMENT_PROVIDER:
                return $this->dataProvider->getProviders();
            default:
                throw new \RuntimeException('Invalid resource DTO class.');
        }
    }

    private function checkImageFormat(): array
    {
        $dirContents = $this->getDirContent(self::RESOURCES_PATH);
        $errors = [];

        foreach ($dirContents as $content) {
            $resourceContent = \sprintf("%s/%s", self::RESOURCES_PATH, $content);

            if (!\is_dir($resourceContent)) {
                $error = $this->checkFileFormat(self::RESOURCES_PATH, $content);
                null === $error ?: $errors[] = $error;

                continue;
            }

            $dirContent = $this->getDirContent($resourceContent);

            foreach ($dirContent as $dir){
                $filePath = \sprintf("%s/%s", $resourceContent, $dir);
                if (!\is_dir($filePath)) {
                    $error = $this->checkFileFormat(self::RESOURCES_PATH, $dir);
                    null === $error ?: $errors[] = $error;

                    continue;
                }

                $files = $this->getDirContent($filePath);

                foreach ($files as $file) {
                    $error = $this->checkFileFormat($filePath, $file);
                    null === $error ?: $errors[] = $error;
                }
            }
        }

        return $errors;
    }

    private function getDirContent(string $path): array
    {
        return \array_filter(\scandir($path), static function ($file) {
            return !\in_array($file, ['.', '..']);
        });
    }

    private function checkFileFormat(string $fileDirectory, string $file): ?string
    {
        $filePath = \sprintf("%s/%s", $fileDirectory, $file);
        $format = \strtolower(\pathinfo($filePath, PATHINFO_EXTENSION));

        if (
            self::MARKDOWN_FORMAT !== $format
            && !\in_array($format, self::ALLOWED_IMAGE_FORMAT, true)
        ) {
            return $filePath;
        }

        return null;
    }
}
