<?php

require_once '../src/X5.php';

$x5 = new \Acj\X5\X5();

$chars = $x5->dumpChars();

var_dump($chars);
