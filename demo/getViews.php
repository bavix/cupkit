<?php

/**
 * @var \Bavix\CupKit\Client $client
 */
$client = require __DIR__ . '/client.php';

foreach ($client->getViews('test') as $view) {
    var_dump($view);
}
