<?php

require_once '../src/X5.php';

$specials = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'specials.php';

for($i = 2; $i <= 4; $i++) {
    foreach ($specials as $key => $value) {
        $x5 = new \Acj\X5\X5($key);
        $x5->setPower($i);

        if(isset($_GET['save'])) {
            $x5->save();
        }
    }
}
