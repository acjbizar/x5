<?php

require_once '../src/X5.php';

$x5 = new \Acj\X5\X5();

$power = $x5->getPower();

var_dump($power);

$x5->setPower(5);

$power = $x5->getPower();

var_dump($power);
