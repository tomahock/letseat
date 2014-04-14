<?php
require '../vendor/autoload.php';

$client = new \Everyman\Neo4j\Client();

$object = \LetsEat\Model\Node\Contact::getByMobile($client, (string) 963740561);
var_dump($object);