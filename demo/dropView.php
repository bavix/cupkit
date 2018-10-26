<?php

/**
 * @var \Bavix\CupKit\Client $client
 */
$client = require __DIR__ . '/client.php';

var_dump($client->dropView('test', 'test'));
