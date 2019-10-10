<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cocur\Slugify\Slugify;

class ParserCommand extends Command
{
    protected static $defaultName = 'app:parse';

    private const PAYOUT_JSON_PATH = __DIR__ . '/../../../../data/payout_services.json';
    private const HASH = '#';
    private const DOT = '.';

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

            if($serviceFields !== null && count($serviceFields) === 1 && $serviceFields[0]['key'] === 'client_id') {

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

        if(strpos($example, self::HASH) !== false || strpos($ruLabel, self::HASH) !== false) {
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

        $explodedFields = $this->getLabelsFromString($serviceFields['label'], $delimiter);

        foreach($explodedFields as $position => $explodedField) {
            $fields[] = [
                'key' => $explodedField['key'],
                'type' => $serviceFields['type'],
                'label' => [
                    'en' => $explodedField['en'],
                    'ru' => $explodedField['ru'],
                    'uk' => $explodedField['uk'],
                ],
                'hint' => [
                    'en' => $explodedField['en'],
                    'ru' => $explodedField['ru'],
                    'uk' => $explodedField['uk'],
                ],
                'regexp' => $serviceFields['regexp'],
                'required' => $serviceFields['required'],
                'position' => ++$position
            ];
        }

        return $fields;
    }

    private function getLabelsFromString(array $labels, string $delimiter): array
    {
        $slugify = new Slugify(['separator' => '_']);
        $explodedLabels = [];

        $labelEn = explode($delimiter, $labels['en']);
        $labelRu = explode($delimiter, $labels['ru']);
        $labelUk = explode($delimiter, $labels['uk']);

        $count = count($labelEn);

        for ($i = 0; $i < $count; $i++) {
            $explodedLabels[$i] = [
                'key' => $slugify->slugify(trim($labelEn[$i])),
                'en' => trim($labelEn[$i]),
                'ru' => trim($labelRu[$i]),
                'uk' => trim($labelUk[$i]),
            ];
        }

        return $explodedLabels;
    }

    private function isEmail(?string $str): bool
    {
        return preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/m', $str);
    }

    private function getServices()
    {
        $json = file_get_contents(self::PAYOUT_JSON_PATH);

        return json_decode($json, true);
    }

    private function save(array $services): void
    {
        $fp = fopen(self::PAYOUT_JSON_PATH, 'wb');
        fwrite($fp, json_encode($services, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        fclose($fp);
    }

}