<?php

require_once dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Acj\X5\X5;

$x5 = new X5;

$power = $x5->getPower();

var_dump($power);

$x5->setPower(5);

$power = $x5->getPower();

var_dump($power);
