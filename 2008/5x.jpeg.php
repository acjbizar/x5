<?php

$letter = !empty($_GET['letter']) ? $_GET['letter'] : 'A';

switch($letter) {
	case 'A':
		$letter = array(0,1,1,1,0,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1);
		break;
	case 'B':
		$letter = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,1,1,1,0);
		break;
	case 'C':
		$letter = array(0,1,1,1,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,1,1,1,1);
		break;
	case 'D':
		$letter = array(1,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,1,1,1,0);
		break;
	case 'E':
		$letter = array(1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,1,1,1,1);
		break;
	case'F':
		$letter = array(1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0);
		break;
	case'G':
		$letter = array(0,1,1,1,1,1,0,0,0,0,1,0,0,1,1,1,0,0,0,1,0,1,1,1,0);
		break;
	case 'H':
		$letter = array(1,0,0,0,1,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1);
		break;
	case 'I':
		$letter = array(0,1,1,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,1,0);
		break;
	case 'J':
		$letter = array(0,0,1,1,1,0,0,0,0,1,0,0,0,0,1,1,0,0,0,1,0,1,1,1,0);
		break;
	case 'K':
		$letter = array(1,0,0,1,0,1,0,1,0,0,1,1,0,0,0,1,0,1,0,0,1,0,0,1,0);
		break;
	case 'L':
		$letter = array(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,1,1,1,1);
		break;
	case 'M':
		$letter = array(1,0,0,0,1,1,1,0,1,1,1,0,1,0,1,1,0,0,0,1,1,0,0,0,1);
		break;
	case 'N':
		$letter = array(1,0,0,0,1,1,1,0,0,1,1,0,1,0,1,1,0,0,1,1,1,0,0,0,1);
		break;
	case 'O':
		$letter = array(0,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0);
		break;
	case 'P':
		$letter = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,0,1,0,0,0,0);
		break;
	case 'Q':
		$letter = array(0,1,1,1,0,1,0,0,0,1,1,0,1,0,1,1,0,0,1,0,0,1,1,0,1);
		break;
	case 'R':
		$letter = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,0,0,0,1);
		break;
	case 'S':
		$letter = array(0,1,1,1,1,1,0,0,0,0,0,1,1,1,0,0,0,0,0,1,1,1,1,1,0);
		break;
	case 'T':
		$letter = array(1,1,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0);
		break;
	case 'U':
		$letter = array(1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0);
		break;
	case 'V':
		$letter = array(1,0,0,0,1,1,0,0,0,1,0,1,0,1,0,0,1,0,1,0,0,0,1,0,0);
		break;
	case 'W':
		$letter = array(1,0,0,0,1,1,0,0,0,1,1,0,1,0,1,0,1,0,1,0,0,1,0,1,0);
		break;
	case 'X':
		$letter = array(1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,1,0,1,0,1,0,0,0,1);
		break;
	case 'Y':
		$letter = array(1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0);
		break;
	case 'Z':
		$letter = array(1,1,1,1,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,1,1,1,1);
		break;
	case '1':
		$letter = array(0,0,1,0,0,0,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,1,0);
		break;
	default:
		$letter = array(0,1,1,1,0,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1);
		break;
} 

header("Content-type: image/jpeg");

$x = 0;
$y = 0;
$scale = 100;
$j = 0;

$width = $scale * 5;
$height = $scale * 5 ;

$image = imagecreate($width, $height);

$background = imagecolorallocate($image, 252, 252, 252);
$color = imagecolorallocate($image, 0, 0, 0);

for ($i = 0; $i < 25; $i++) {
	if ($j > 4) {
		$y = $y + $scale;
		$x = 0;
		if ($letter[$i] == 1) {
			imagefilledrectangle($image, $x, $y, $x + $scale, $y + $scale, imagecolorallocate($image, 0, 0, mt_rand(0, 255)));
		} else {
			imagefilledrectangle($image, $x, $y, $x + $scale, $y + $scale, $background);
		}
		$j = 0;
	} else {
		if ($letter[$i] == 1) {
			imagefilledrectangle($image, $x, $y, $x + $scale, $y + $scale, imagecolorallocate($image, 0, 0, mt_rand(0, 255)));
		} else {
			imagefilledrectangle($image, $x, $y, $x + $scale, $y + $scale, $background);
		}
	}
	$j++;
	$x = $x + $scale;
}

imagejpeg($image);
imagedestroy($image);
