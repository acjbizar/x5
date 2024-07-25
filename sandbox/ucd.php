<?php

require_once '../vendor/autoload.php';
//$chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';

use UCD\Database;
use UCD\Unicode\Codepoint;

$database = Database::fromDisk();
$string = ' !"#$%\'()+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\\]^_{|}~·÷…∴∵∞⋮⌂■□▬▭▮▯▲△▴▶▷▼▽◀◁◆◇◢◣◤◥◫◻☉☰☱☲☳☴☵☶☷♡♥✝𓃑';
$codepoints = Codepoint\Collection::fromUTF8($string);
$assigned = $database->getByCodepoints($codepoints);

$array = [];

foreach ($assigned->getCharacters() as $character) {
    $properties = $character->getProperties();
    $general = $properties->getGeneral();
    $names = $general->getNames();

    printf("%s: %s\n", $character->getCodepoint(), $names->getPrimary());

    echo '<pre>';
    var_dump($properties);
    echo '</pre>';

    $array[$character->getCodepointValue()] = [
        'name' => $names->getPrimary()->getValue(),
        'block' => $general->getBlock()->getValue()
    ];
}

echo '<!doctype html><meta charset="utf-8"><pre>';
var_dump($array);
echo '</pre>';

if(isset($_GET['save'])) {
    file_put_contents('../data/ucd.json', json_encode($array, JSON_PRETTY_PRINT)) or die('Error');
}
