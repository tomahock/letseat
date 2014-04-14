<?php
require '../vendor/autoload.php';

$position = new \LetsEat\Type\Position(38.725937,-9.138664);
\LetsEat\Helper\Weather::getForPosition($position);