<?php

include $_composer_autoload_path ?? __DIR__ . '/../vendor/autoload.php';

require_once '../src/X5.php';
require_once '../src/X5Vector.php';

$json = file_get_contents(dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'set.json');
$set = json_decode($json, true);
$static = array_diff($set['algorithmic'], $set['dynamic']);

//var_dump($static);exit;

for($i = 1; $i <= 3; $i++) {
    foreach ($static as $key => $value) {
        $x5 = new \Acj\X5\X5Vector($value);
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
