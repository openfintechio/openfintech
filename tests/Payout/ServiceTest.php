<?php

declare(strict_types=1);

namespace Tests\Payout;

use Oft\Generator\Dto\CurrencyDto;
use Oft\Generator\Dto\PayoutServiceDto;
use Oft\Generator\Enums\ResourceType;
use Tests\AbstractDataTest;
use Tests\Relation;

final class ServiceTest extends AbstractDataTest
{
    public function test_check_services_for_duplicates(): void
    {
        $this->assertResourceHasNoDuplication(
            ResourceType::PAYOUT_SERVICE,
            'Duplicate payout services:'
        );
    }

    public function test_service_with_method_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYOUT_SERVICE,
            'method',
            ResourceType::PAYOUT_METHOD
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'PAYOUT METHOD'),
            'in services'
        );
    }

    public function test_service_with_currency_relation(): void
    {
        $relation = new Relation(
            ResourceType::PAYOUT_SERVICE,
            'currency',
            ResourceType::CURRENCY
        );

        $this->assertCorrectRelationWithOne(
            $relation,
            \sprintf(self::NOT_EXISTENT_ERROR_HEADER_TEMPLATE, 'CURRENCY'),
            'in services'
        );
    }

    public function test_service_limit_exponent(): void
    {
        /** @var PayoutServiceDto[] $services */
        $services = $this->dataProvider->getPayoutServices();
        /** @var CurrencyDto[] $services */
        $currencies = $this->dataProvider->getCurrencies();
        /** @var CurrencyDto $currency */
        $mappedCurrencies = \array_combine(
            \array_map(static fn($currency) => $currency->code, $currencies),
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
            $message = 'Wrong exponent in payout services:' . PHP_EOL;
            foreach ($servicesWithWrongExponent as $service => $rightExponent) {
                $message .= \sprintf("\t - %s | correct exponent: %s", $service, $rightExponent) . PHP_EOL;
            }

            $this->fail($message);
        }

        self::assertTrue(true);
    }
}
