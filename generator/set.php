<?php

require_once dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Acj\X5\X5;

$x5 = new X5;

$chars = $x5->getCharsAsString();

if(isset($_GET['save'])) {
    $json = file_get_contents(dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'set.json');
    $set = json_decode($json);
    $set->chars = $chars;
    $json = json_encode($set, JSON_PRETTY_PRINT);

    file_put_contents(dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'set.json', $json);

    echo 'Profit!';
} else {
    var_dump($chars);
}
