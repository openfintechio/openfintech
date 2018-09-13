<?php

use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;


$loader = require __DIR__ . '/../vendor/autoload.php';

$data = ['currencies', 'vendors', 'payout_methods', 'payment_methods', 'payment_methods', 'providers'];

foreach ($data as $name) {
    $validator = new Validator;
    $file = json_decode(file_get_contents('./data/' . $name . '.json'));

    $validator->validate($file, (object)['$ref' => 'file://' . realpath('./schemas/' . $name . '_schema.json')], Constraint::CHECK_MODE_APPLY_DEFAULTS);

    if ($validator->isValid()) {
        echo "The " . $name . " validates against the schema.\n";
    } else {
        echo $name . " shema does not valid. Violations:\n";
        foreach ($validator->getErrors() as $error) {
            echo sprintf("[%s] %s\n", $error['property'], $error['message']);
        }
    }
}