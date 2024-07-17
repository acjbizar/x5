<?php

require_once '../src/X5.php';

for($i = 1; $i <= 5; $i++) {
    $x5 = new \Acj\X5\X5('squared-circle');
    $x5->setPower($i);

    if(isset($_GET['save'])) {
        $x5->save();
    }
}
