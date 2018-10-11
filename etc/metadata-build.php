<?php
declare(strict_types=1);


$directory = new RecursiveDirectoryIterator(__DIR__ . '/../resources');
$iterator = new RecursiveIteratorIterator($directory);
$regex = new RegexIterator($iterator,
    '/^.+\/(?P<resource_type>.+)\/(?P<resource_name>.+)\/(?P<type>logo|icon)\.(?P<ext>svg|png)$/i',
    RecursiveRegexIterator::GET_MATCH);

$resources = [];

foreach ($regex as $resource) {
    $data = $resources[$resource['resource_type']][$resource['resource_name']][$resource['type']] ?? [
            'svg' => false,
            'png' => false
        ];

    if ($resource['ext'] === 'svg') {
        $data['svg'] = true;
    }

    if ($resource['ext'] === 'png') {
        $data['png'] = true;
    }

    $resources[$resource['resource_type']][$resource['resource_name']][$resource['type']] = $data;
}

foreach ($resources as $type => $resourceData) {
    file_put_contents($type . '.json', json_encode($resourceData));
}