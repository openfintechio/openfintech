<?php

declare(strict_types=1);

namespace Tests\Payment;

use Oft\Generator\Dto\CurrencyDto;
use Oft\Generator\Dto\PaymentServiceDto;
use Oft\Generator\Enums\ResourceType;
use Tests\AbstractDataTest;
use Tests\Relation;

final class ServiceTest extends AbstractDataTest
{
    public function test_check_services_for_duplicates():void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYMENT_SERVICE,
            'Duplicate payment services:'
        );
    }

    public function test_service_with_method_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_SERVICE,
            'method',
            ResourceType::PAYMENT_METHOD
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'PAYMENT METHOD'),
            'in payment services'
        );
    }

    public function test_service_with_currency_relation():void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_SERVICE,
            'currency',
            ResourceType::CURRENCY
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'CURRENCY'),
            'in payment services'
        );
    }

    public function test_service_with_flow_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYMENT_SERVICE,
            'flow',
            ResourceType::PAYMENT_FLOW
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'FLOW'),
            'payment services'
        );
    }

    public function test_service_limit_exponent(): void
    {
        /** @var PaymentServiceDto[] $services */
        $services = $this->dataProvider->getPaymentServices();
        /** @var CurrencyDto[] $services */
        $currencies = $this->dataProvider->getCurrencies();
        /** @var CurrencyDto $currency */
        $mappedCurrencies = \array_combine(
            \array_map(static fn ($currency) => $currency->code, $currencies),
            $currencies
        );
        $servicesWithWrongExponent = [];

        foreach ($services as $service) {
            /** @var CurrencyDto|null $currencyOfService */
            $currencyOfService = $mappedCurrencies[$service->currency] ?? null;

            if (null === $currencyOfService) {
                continue;
            }

            $amountMaxExponent = (int) strpos(strrev((string) $service->amountMax), ".");
            $amountMinExponent = (int) strpos(strrev((string) $service->amountMin), ".");

            if (
                $amountMinExponent > $currencyOfService->exponent
                || $amountMaxExponent > $currencyOfService->exponent
            ) {
                $servicesWithWrongExponent[$service->code] = $currencyOfService->exponent;
            }
        }

        if (0 !== \count($servicesWithWrongExponent)) {
            $message = 'Wrong exponent in payment services:' . PHP_EOL;
            foreach ($servicesWithWrongExponent as $service => $rightExponent) {
                $message .= \sprintf("\t - %s | correct exponent: %s", $service, $rightExponent) . PHP_EOL;
            }

            $this->fail($message);
        }

        self::assertTrue(true);
    }
}
