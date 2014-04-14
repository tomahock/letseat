<?php
require '../vendor/autoload.php';

use LetsEat\Helper;
use LetsEat\Type;

$position = new Type\Position(39.412558, -9.135798);
$accuracy = 20;
$venues = Helper\Venue::get($position, $accuracy);

print_r($venues);
