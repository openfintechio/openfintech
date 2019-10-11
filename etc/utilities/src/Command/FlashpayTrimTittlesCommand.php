<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cocur\Slugify\Slugify;


class FlashpayTrimTittlesCommand extends Command
{
    protected static $defaultName = 'flashpay:trim-payout-methods-titles';

    private const PAYOUT_SERVICES_JSON_PATH = __DIR__ . '/../../../../data/payout_methods.json';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->updatePayoutServices();
        $output->writeln('Success');
    }

    private function updatePayoutServices()
    {
        $services = $this->getServices();

        foreach($services as $key => $service) {
            $service['name'] = $this->trimTitles($service['name']);
            $services[$key] = $service;
        }

        $this->save($services);
    }

    private function trimTitles(array $serviceName): array
    {
        return array_merge([
            'en' => trim($serviceName['en']),
            'ru' => trim($serviceName['ru'] ?? $serviceName['en']),
            'uk' => trim($serviceName['uk'] ?? $serviceName['en']),
        ], $serviceName);
    }

    private function getServices(): array
    {
        $json = file_get_contents(self::PAYOUT_SERVICES_JSON_PATH);

        return json_decode($json, true);
    }

    private function save(array $services): void
    {
        $json = preg_replace_callback ('/^ +/m', static function ($m) {
            return str_repeat (' ', strlen ($m[0]) / 2);
        }, json_encode ($services, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

        $fp = fopen(self::PAYOUT_SERVICES_JSON_PATH, 'wb');
        fwrite($fp, $json);
        fclose($fp);
    }
}