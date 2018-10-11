<?php
declare(strict_types=1);

namespace Oft\Provider;


final class DataProvider
{
    private static $cache = [];

    public static function getResources(string $resourceType): array
    {
        return self::loadData($resourceType);
    }

    public static function getResourceData(string $resourceType, string $resourceCode)
    {
        if (false === self::dataExists($resourceType, $resourceCode)) {
            throw new \RuntimeException(
                sprintf('Resource with type %s and code %s not found.', $resourceType, $resourceCode)
            );
        }

        $data = self::loadData($resourceType);

        return $data[$resourceCode];
    }

    public static function dataExists(string $resourceType, string $resourceCode): bool
    {
        $data = self::loadData($resourceType);

        return array_key_exists($resourceCode, $data);
    }

    private static function loadData(string $resourceType): array
    {
        if (false === array_key_exists($resourceType, self::$cache)) {
            $path = __DIR__ . '/data/' . $resourceType . '.json';

            if (false === file_exists($path)) {
                throw new \RuntimeException('Resource does not exist.');
            }

            $data = json_decode(file_get_contents($path), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Malformed json data provided.');
            }

            $codes = array_column($data, 'code');
            $data = array_combine($codes, $data);

            self::$cache[$resourceType] = $data;
        }

        return self::$cache[$resourceType];
    }
}