<?php

require_once '../src/X5.php';

$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';

$color = !empty($_GET['color']) ? $_GET['color'] : '#0000ff';
$input = !empty($_GET['p']) && is_array($_GET['p']) ? $_GET['p'] : [];
$power = !empty($_GET['power']) ? intval($_GET['power']) : 3;

$x5 = new \Acj\X5\X5('custom');
$x5->setColor($color);
$x5->setInput($input);
$x5->setPower($power);
$x5->parse();
