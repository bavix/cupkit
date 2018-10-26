<?php

/**
 * @var \Bavix\CupKit\Client $client
 */
$client = require __DIR__ . '/client.php';

$image = 'https://coder-booster.ru/content/learning/php-practice/sending-http-request-using-file-functions/otpravka-http-zaprosa-cherez-fajlovye-funktsii-v-php.jpg';

var_dump($client->createImage('test', $image));
