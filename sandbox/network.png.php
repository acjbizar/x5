<?php

require_once '../src/X5.php';

$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';

$value = !empty($_GET['value']) ? intval($_GET['value']) : 0;
$power = !empty($_GET['power']) ? intval($_GET['power']) : 3;

$x5 = new \Acj\X5\X5('network');
$x5->setPower($power);
$x5->setValue($value);
$x5->parse();
