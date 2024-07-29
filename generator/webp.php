<?php

require_once '../src/X5.php';

// Don't bother with this. The results are somehow larger than their PNG equivalents.

$key = 'notdef';

if(isset($_GET['save'])) {

    for($i = 1; $i <= 5; $i++) {
        $x5 = new \Acj\X5\X5($key);
        $x5->setExtension('webp');
        $x5->setPower($i);
        $x5->save();
    }

} else {
    $power = !empty($_GET['power']) ? intval($_GET['power']) : 3;

    $x5 = new \Acj\X5\X5($key);
    $x5->setExtension('webp');
    $x5->setPower($power);
    $x5->parse();
}
