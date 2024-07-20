<?php

require_once '../src/X5.php';

$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';

$i = !empty($_GET['i']) ? $_GET['i'] : null;
$power = !empty($_GET['power']) ? intval($_GET['power']) : 3;

$x5 = new \Acj\X5\X5('identicon');
$x5->setIdentifier($i);
$x5->setPower($power);
$x5->parse();
