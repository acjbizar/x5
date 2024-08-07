<?php

set_time_limit(600);

require_once '../src/X5.php';

$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';

$key = 'tripletau';

if(isset($_GET['save'])) {

    for($i = 1; $i <= 5; $i++) {
        $x5 = new \Acj\X5\X5($key);
        $x5->setPower($i);
        $x5->setTransparent(true);
        $x5->save();

        $x5 = new \Acj\X5\X5($key);
        $x5->setPower($i);
        $x5->setTransparent(false);
        $x5->save();
    }

} else {
    $power = !empty($_GET['power']) ? intval($_GET['power']) : 3;

    $x5 = new \Acj\X5\X5($key);
    $x5->setPower($power);
    $x5->setTransparent(false);
    $x5->parse();
}