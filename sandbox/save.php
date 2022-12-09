<?php

require_once '../src/X5.php';

$x5 = new \Acj\X5\X5(0x41);
$x5->setPower(4);

if(isset($_GET['save'])) {
    $x5->save();
}

//$x5->parse();