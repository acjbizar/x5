<?php

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

require_once '../src/X5.php';
require_once '../src/X5Vector.php';

$power = !empty($_GET['power']) ? intval($_GET['power']) : 3;

$key = 0x41;
$key = 'LE';

$x5 = new \Acj\X5\X5Vector($key);
$x5->setPower($power);
$x5->setTransparent(false);

if(isset($_GET['save'])) {
    $x5->save();
}

$x5->parse();