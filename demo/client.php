<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$clientCredentials = new \Bavix\CupKit\ClientCredentials(
    'http://corundum.local',
    1,
    'qNYA4AF9wxQtDdX6XwARkPzIEDFlPkBq93t7BI68'
);

$identity = new \Bavix\CupKit\Identity(
    $clientCredentials,
    'info@babichev.net',
    '12345'
);

return new \Bavix\CupKit\Client($identity);
