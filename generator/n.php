<?php

require_once '../src/X5.php';

for($i = 2; $i <= 5; $i++) {
    $x5 = new \Acj\X5\X5('n');
    $x5->setPower($i);

    if(isset($_GET['save'])) {
        $x5->save();
    }
}
