<?php // I:\ptw\lab-f\yaml.php

$data = [
    'name' => 'Jakub Sibora',
    'index' => '55694',
    'date' => date(DATE_ATOM),
];

$yaml = yaml_emit($data);

echo $yaml;
