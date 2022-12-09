<?php

require_once dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Acj\X5\X5;

$x5 = new X5('22');
$x5->setPower(4);

$x5->parse();
