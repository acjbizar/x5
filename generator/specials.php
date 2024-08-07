<?php

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

require_once '../src/X5.php';
require_once '../src/X5Vector.php';

$specials = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'specials.php';

for($i = 1; $i <= 5; $i++) {
    foreach ($specials as $key => $value) {
        $x5 = new \Acj\X5\X5Vector($key);
        $x5->setPower($i);
        $x5->setTransparent(false);

        if(isset($_GET['save'])) {
            $x5->save();
            echo 'Saved: ';
            echo $x5->filename;
            echo '<br>';
        }
    }
}
