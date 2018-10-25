<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$clientCredentials = new \Bavix\CupKit\ClientCredentials(
    'http://corundum.local',
    1,
    'pass'
);

$identity = new \Bavix\CupKit\Identity(
    $clientCredentials,
    'test@corundum.local',
    'test@corundum.local'
);

$client = new \Bavix\CupKit\Client($identity);

var_dump($client->getBuckets());
