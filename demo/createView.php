<?php

/**
 * @var \Bavix\CupKit\Client $client
 */
$client = require __DIR__ . '/client.php';

var_dump($client->createView('test', [
    'name' => 'test',
    'type' => 'none',
    'width' => 250,
    'height' => 250,
]));
