<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrimPayoutFieldsCommand extends Command
{
    protected static $defaultName = 'app:trim-payout-fields';

    private const PAYOUT_METHODS_JSON_PATH = __DIR__ . '/../../../../data/payout_methods.json';
    private const PAYOUT_SERVICES_JSON_PATH = __DIR__ . '/../../../../data/payout_services.json';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Updating...');
        $this->updatePayoutServices();
        $this->updatePayoutMethods();
        $output->writeln('Success!');
    }


    private function updatePayoutServices(): void
    {
        $services = $this->getData(self::PAYOUT_SERVICES_JSON_PATH);

        foreach($services as $key => $service) {

            // some services don't have fields
            if(array_key_exists('fields', $service)) {
                $service['fields'] = $this->trimServiceFields($service['fields']);
                $services[$key] = $service;
            }
        }

        $this->save($services, self::PAYOUT_SERVICES_JSON_PATH);
    }

    private function trimServiceFields(array $serviceFields): array
    {
        $fields = [];

        foreach($serviceFields as $field) {

            $label = $field['label'];

            $field['label'] = array_merge([
                'en' => trim($label['en']),
                'ru' => trim($label['ru'] ?? $label['en']),
                'uk' => trim($label['uk'] ?? $label['en']),
            ], $label);

            $hint = $field['hint'];

            $field['hint'] = array_merge([
                'en' => trim($hint['en']),
                'ru' => trim($hint['ru'] ?? $hint['en']),
                'uk' => trim($hint['uk'] ?? $hint['en']),
            ], $hint);

            $fields[] = $field;
        }

        return $fields;
    }

    private function updatePayoutMethods(): void
    {
        $methods = $this->getData(self::PAYOUT_METHODS_JSON_PATH);

        foreach($methods as $key => $method) {
            $method['name'] = $this->trimServiceName($method['name']);
            $methods[$key] = $method;
        }

        $this->save($methods, self::PAYOUT_METHODS_JSON_PATH);
    }

    private function trimServiceName(array $serviceName): array
    {
        return array_merge([
            'en' => trim($serviceName['en']),
            'ru' => trim($serviceName['ru'] ?? $serviceName['en']),
            'uk' => trim($serviceName['uk'] ?? $serviceName['en']),
        ], $serviceName);
    }

    private function getData(string $path): array
    {
        $json = file_get_contents($path);

        $data = json_decode($json, true);

        foreach($data as $index => $param) {
            if(!array_key_exists('amount_min', $param)) {
                continue;
            }

            $param['amount_min'] = $this->formatFloatValues($param['amount_min']);
            $param['amount_max'] = $this->formatFloatValues($param['amount_max']);

            $data[$index] = $param;
        }

        return $data;
    }

    private function save(array $services, string $path): void
    {
        $json = preg_replace_callback ('/^ +/m', static function ($m) {
            return str_repeat (' ', strlen ($m[0]) / 2);
        }, json_encode ($services,  JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_PRESERVE_ZERO_FRACTION));

        $fp = fopen($path, 'wb');
        fwrite($fp, $json);
        fclose($fp);
    }

    private function formatFloatValues($float) {
        $parts = explode('E', $float);

        if(count($parts) === 2){
            $exp = abs(end($parts)) + strlen($parts[0]);
            $decimal = number_format($float, $exp);
            return rtrim($decimal, '.0');
        }

        return (string) $float;
    }
}