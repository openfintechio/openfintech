<?php

$z = json_decode(file_get_contents('data/payment_providers.json'));
$a = [];
foreach ($z as $vendor){
    $vendor = (array)$vendor;
    if(array_key_exists('countries', $vendor)) {
        if (count($vendor['countries']) > 100) {
            unset($vendor['countries']);
        }
    }
    $a[] = $vendor;
}

$data = json_encode($a, JSON_UNESCAPED_UNICODE);

$fp = fopen("data/payment_providers.json", "wt");
fwrite($fp, $data);
