<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cocur\Slugify\Slugify;


class FlashpaySplitFieldsCommand extends Command
{
    protected static $defaultName = 'flashpay:split-fields';

    private const PAYOUT_SERVICES_JSON_PATH = __DIR__ . '/../../../../data/payout_services.json';
    private const HASH = '#';
    private const DOT = '.';
    private const CLIENT_ID = 'client_id';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->updatePayoutServices();
        $output->writeln(sprintf('Updated %s', $count));
    }

    private function updatePayoutServices(): int
    {
        $services = $this->getServices();
        $count = 0;

        foreach($services as $key => $service) {

            $serviceFields = $service['fields'] ?? null;

            if($serviceFields !== null && count($serviceFields) === 1 && $serviceFields[0]['key'] === self::CLIENT_ID) {

                $delimiter = $this->getServiceFieldsDelimiter($serviceFields[0]);

                if($delimiter !== null) {
                    $service['fields'] = $this->getFieldsFromString($serviceFields[0], $delimiter);
                    $service['field_delimiter'] = $delimiter;
                    $services[$key] = $service;
                    $count++;
                }
            }
        }

        $this->save($services);

        return $count;
    }

    private function getServiceFieldsDelimiter(array $field): ?string
    {
        $example = $field['example'] ?? null;
        $ruLabel = $field['label']['ru'] ?? null;

        if(strpos($example, self::HASH) !== false && strpos($ruLabel, self::HASH) !== false) {
            return self::HASH;
        }

        if(!$this->isEmail($example) && (strpos($example, self::DOT) !== false && strpos($ruLabel, self::DOT) !== false) ) {
            return self::DOT;
        }

        return null;
    }
    
    private function getFieldsFromString(array $serviceFields, string $delimiter): array
    {
        $fields = [];

        $explodedFields = $this->getLabelsFromString($serviceFields['label'], $delimiter, $serviceFields['example']);

        foreach($explodedFields as $position => $explodedField) {
            $fields[] = [
                'key' => $explodedField['key'],
                'type' => $serviceFields['type'],
                'label' => [
                    'en' => $explodedField['en'],
                    'ru' => $explodedField['ru'],
                    'uk' => $explodedField['uk'],
                ],
                'regexp' => $serviceFields['regexp'],
                'required' => $serviceFields['required'],
                'position' => ++$position,
                'hint' => [
                    'en' => $explodedField['en'],
                    'ru' => $explodedField['ru'],
                    'uk' => $explodedField['uk'],
                ],
                //'example' => $explodedField['example'],
            ];
        }

        return $fields;
    }

    private function getLabelsFromString(array $labels, string $delimiter, string $examples): array
    {
        $slugify = new Slugify(['separator' => '_']);
        $explodedLabels = [];

        $labelEn = explode($delimiter, $labels['en']);
        $labelRu = explode($delimiter, $labels['ru']);
        $labelUk = explode($delimiter, $labels['uk']);
        $example = explode($delimiter, $examples);

        $count = count($labelEn);

        for ($i = 0; $i < $count; $i++) {
            $explodedLabels[$i] = [
                'key' => $slugify->slugify(trim($labelEn[$i])),
                'en' => trim($labelEn[$i]),
                'ru' => trim($labelRu[$i]),
                'uk' => trim($labelUk[$i]),
                //'example' => $example[$i],
            ];
        }

        return $explodedLabels;
    }

    private function isEmail(?string $str): bool
    {
        return preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/m', $str) ? true : false;
    }

    private function getServices(): array
    {
        $json = file_get_contents(self::PAYOUT_SERVICES_JSON_PATH);

        return json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
    }

    private function save(array $services): void
    {
        $json = preg_replace_callback ('/^ +/m', static function ($m) {
            return str_repeat (' ', strlen ($m[0]) / 2);
        }, json_encode ($services, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_PRESERVE_ZERO_FRACTION));

        $fp = fopen(self::PAYOUT_SERVICES_JSON_PATH, 'wb');
        fwrite($fp, $json);
        fclose($fp);
    }
}