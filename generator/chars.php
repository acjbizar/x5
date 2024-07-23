<?php

set_time_limit(600);

require_once '../src/X5.php';

$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';

for($i = 1; $i <= 5; $i++) {
    foreach ($chars as $key => $value) {
        $x5 = new \Acj\X5\X5($key);
        $x5->setTransparent(true);
        $x5->setPower($i);

        if(isset($_GET['save'])) {
            $x5->save();
            echo 'Saved: ';
            echo $x5->filename;
            echo "\r\n";
        } else {
            echo 'Not saving: ';
            echo $x5->filename;
            echo "\r\n";
        }
    }
}
