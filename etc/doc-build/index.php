<?php

require(__DIR__ . '/vendor/autoload.php');

use Oft\Generator\DocBuilder;

$output_path = getopt("p:")['p'] ?? __DIR__;

$builder = new DocBuilder($output_path);
$builder->build();