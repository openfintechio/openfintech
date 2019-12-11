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
    private const SLASH = '/';
    private const CLIENT_ID = 'client_id'; // flashpay service field key

    /** @var array */
    private $hardcoded = [
        'uk-zapadnaia-g-mariupol_uah' => [self::DOT, self::SLASH],
        'zhkp-zhitlo-tsentr_uah' => [self::DOT, self::DOT], // bad example
    ];

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

            // select flashpay services
            if($serviceFields !== null && count($serviceFields) === 1 && $serviceFields[0]['key'] === self::CLIENT_ID) {
                $delimiters = $this->getServiceFieldsDelimiters($service['code'], $serviceFields[0]);

                if(count($delimiters) !== 0) {
                    $service['fields'] = $this->getFieldsFromString($serviceFields[0], $delimiters);
                    $service['field_delimiters'] = $delimiters;

                    $services[$key] = $service;
                    $count++;
                }
            }
        }

        $this->save($services);

        return $count;
    }

    private function getServiceFieldsDelimiters(string $serviceCode, array $field): array
    {
        if($this->isHardcoded($serviceCode)) {
            return $this->hardcoded[$serviceCode];
        }

        $example = $field['example'] ?? null;
        $ruLabel = $field['label']['ru'] ?? null;

        $delimiter = $this->getMainDelimiter($ruLabel, $example);

        if ($delimiter === null) {
            return [];
        }

        $count = substr_count($ruLabel, $delimiter);

        return array_fill(0, $count, $delimiter);
    }

    private function getMainDelimiter(string $ruLabel, ?string $example): ?string
    {
        if(strpos($example, self::HASH) !== false || strpos($ruLabel, self::HASH) !== false) {
            return self::HASH;
        }

        if(strpos($ruLabel, self::SLASH) !== false) {
            return self::SLASH;
        }

        if(!$this->isEmail($example) && (strpos($example, self::DOT) !== false && strpos($ruLabel, self::DOT) !== false) ) {
            return self::DOT;
        }

        return null;
    }

    private function getFieldsFromString(array $serviceFields, array $delimiters): array
    {
        $fields = [];

        $explodedFields = $this->getLabelsFromString($serviceFields['label'], $delimiters, $serviceFields['example'] ?? '');

        foreach($explodedFields as $position => $explodedField) {
            $field = [
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

            ];

            if (isset($explodedField['example'])) {
                $field['example'] = $explodedField['example'];
            }

            $fields[] = $field;
        }

        return $fields;
    }

    private function getLabelsFromString(array $labels, array $delimiters, string $examples): array
    {
        $slugify = new Slugify(['separator' => '_']);

        $explodedLabels = [];

        // two patterns differ only with `.`
        $pattern_part = (in_array(self::DOT, $delimiters, true))
            ? '([0-9A-zА-яёЁЇїІіЄєҐґ№,\-()\s]+)'
            : '([0-9A-zА-яёЁЇїІіЄєҐґ№.,\-()\s]+)';

        $pattern = '/' . $pattern_part;

        foreach($delimiters as $delimiter) {
            $pattern .= '\\' . $delimiter . $pattern_part;
        }

        $pattern .= '/u';

        $labelEn = $this->getArrayFromString($labels['en'], $pattern);
        $labelRu = $this->getArrayFromString($labels['ru'], $pattern);
        $labelUk = $this->getArrayFromString($labels['uk'], $pattern);
        $exampleList = $this->getArrayFromString($examples, $pattern);

        $labelCount = count($labelEn);

        if ($labelCount !== count($exampleList)) {
            $firstExample = $exampleList[0] ?? '';
            $exampleList = array_fill(0, $labelCount, $firstExample);
        }

        for ($i = 0; $i < $labelCount; $i++) {
            $explodedLabel = [
                'key' => $slugify->slugify(trim($labelEn[$i])),
                'en' => trim($labelEn[$i]),
                'ru' => trim($labelRu[$i]),
                'uk' => trim($labelUk[$i]),
            ];

            $example = '' !== $exampleList[$i] ? $exampleList[$i] : $examples;
            if ('' !== $example) {
                $explodedLabel['example'] = $example;
            }

            $explodedLabels[$i] = $explodedLabel;
        }

        return $explodedLabels;
    }

    private function getArrayFromString(string $str, string $pattern): array
    {
        preg_match($pattern, $str, $matches);
        array_splice($matches,0,1);

        return $matches;
    }

    private function isHardcoded(string $name): bool
    {
        return array_key_exists($name, $this->hardcoded);
    }

    private function isEmail(?string $str): bool
    {
        return preg_match('/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/m', $str) ? true : false;
    }

    private function getServices(): array
    {
        $json = file_get_contents(self::PAYOUT_SERVICES_JSON_PATH);

        $data = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

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

    private function save(array $services): void
    {
        $json = preg_replace_callback ('/^ +/m', function ($m) {
            return str_repeat (' ', strlen ($m[0]) / 2);
        }, json_encode ($services, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_PRESERVE_ZERO_FRACTION));

        $fp = fopen(self::PAYOUT_SERVICES_JSON_PATH, 'wb');
        fwrite($fp, $json);
        fclose($fp);
    }

    private function formatFloatValues($float): string
    {
        $parts = explode('E', $float);

        if(count($parts) === 2){
            $exp = abs(end($parts)) + strlen($parts[0]);
            $decimal = number_format($float, $exp);

            return rtrim($decimal, '.0');
        }

        return (string) $float;
    }
}