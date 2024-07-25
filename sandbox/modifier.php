<?php

use const Acj\X5\MODIFIER_SQUARES;

set_time_limit(600);

require_once '../src/X5.php';

$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
$key = 'n';
$power = !empty($_GET['power']) ? intval($_GET['power']) : 3;
$modifier = \Acj\X5\MODIFIER_SQUARES;

$x5 = new \Acj\X5\X5($key);
$x5->setModifier($modifier);
$x5->setPower($power);
$x5->setTransparent(false);
$x5->parse();
