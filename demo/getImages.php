<?php

/**
 * @var \Bavix\CupKit\Client $client
 */
$client = require __DIR__ . '/client.php';

foreach ($client->getImages('test') as $bucket) {
    var_dump($bucket);
}
