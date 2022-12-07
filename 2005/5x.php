<?php
header("Content-type: image/png");

$x0021 = array(0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0,0,1,0,0); //!
$x0031 = array(0,0,1,0,0,0,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,1,0); //1
$x0041 = array(0,1,1,1,0,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1); //A
$x0042 = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,1,1,1,0); //B
$x0043 = array(0,1,1,1,1,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,1,1,1,1); //C
$x0044 = array(1,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,1,1,1,0); //D
$x0045 = array(1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,1,1,1,1); //E
$x0046 = array(1,1,1,1,1,1,0,0,0,0,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0); //F
$x0047 = array(0,1,1,1,1,1,0,0,0,0,1,0,0,1,1,1,0,0,0,1,0,1,1,1,0); //G
$x0048 = array(1,0,0,0,1,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1); //H
$x0049 = array(0,1,1,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,1,1,1,0); //I
$x004A = array(0,0,1,1,1,0,0,0,0,1,0,0,0,0,1,1,0,0,0,1,0,1,1,1,0); //J
$x004B = array(1,0,0,1,0,1,0,1,0,0,1,1,0,0,0,1,0,1,0,0,1,0,0,1,0); //K
$x004C = array(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,1,1,1,1); //L
$x004D = array(1,0,0,0,1,1,1,0,1,1,1,0,1,0,1,1,0,0,0,1,1,0,0,0,1); //M
$x004E = array(1,0,0,0,1,1,1,0,0,1,1,0,1,0,1,1,0,0,1,1,1,0,0,0,1); //N
$x004F = array(0,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0); //O
$x0050 = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,0,1,0,0,0,0); //P
$x0051 = array(0,1,1,1,0,1,0,0,0,1,1,0,1,0,1,1,0,0,1,0,0,1,1,0,1); //Q
$x0052 = array(1,1,1,1,0,1,0,0,0,1,1,1,1,1,0,1,0,0,0,1,1,0,0,0,1); //R
$x0053 = array(0,1,1,1,1,1,0,0,0,0,0,1,1,1,0,0,0,0,0,1,1,1,1,1,0); //S
$x0054 = array(1,1,1,1,1,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0); //T
$x0055 = array(1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0); //U
$x0056 = array(1,0,0,0,1,1,0,0,0,1,0,1,0,1,0,0,1,0,1,0,0,0,1,0,0); //V
$x0057 = array(1,0,0,0,1,1,0,0,0,1,1,0,1,0,1,0,1,0,1,0,0,1,0,1,0); //W
$x0058 = array(1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,1,0,1,0,1,0,0,0,1); //X
$x0059 = array(1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0); //Y
$x005A = array(1,1,1,1,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,1,1,1,1); //Z
$check = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1); //Checker board for debugging
$black = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
$white = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

if(isset($_GET['letter'])) :
switch($_GET['letter']) {
	case '1':
		$letter = $x0031;
		break;
	case 'A':
		$letter = $x0041;
		break;
	case 'B':
		$letter = $x0042;
		break;
	case 'C':
		$letter = $x0043;
		break;
	case 'D':
		$letter = $x0044;
		break;
	case 'E':
		$letter = $x0045;
		break;
	case 'F':
		$letter = $x0046;
		break;
	case 'G':
		$letter = $x0047;
		break;
	case 'H':
		$letter = $x0048;
		break;
	case 'I':
		$letter = $x0049;
		break;
	case 'J':
		$letter = $x004A;
		break;
	case 'K':
		$letter = $x004B;
		break;
	case 'L':
		$letter = $x004C;
		break;
	case 'M':
		$letter = $x004D;
		break;
	case 'N':
		$letter = $x004E;
		break;
	case 'O':
		$letter = $x004F;
		break;
	case 'P':
		$letter = $x0050;
		break;
	case 'Q':
		$letter = $x0051;
		break;
	case 'R':
		$letter = $x0052;
		break;
	case 'S':
		$letter = $x0053;
		break;
	case 'T':
		$letter = $x0054;
		break;
	case 'U':
		$letter = $x0055;
		break;
	case 'V':
		$letter = $x0056;
		break;
	case 'W':
		$letter = $x0057;
		break;
	case 'X':
		$letter = $x0058;
		break;
	case 'Y':
		$letter = $x0059;
		break;
	case 'Z':
		$letter = $x005A;
		break;
	default:
		$letter = $x0041;
		break;
}
else :
switch(rand(1, 25)) {
	case 1:
		$letter = $x0041;
		break;
	case 2:
		$letter = $x0042;
		break;
	case 3:
		$letter = $x0043;
		break;
	case 4:
		$letter = $x0044;
		break;
	case 5:
		$letter = $x0045;
		break;
	case 6:
		$letter = $x0046;
		break;
	case 7:
		$letter = $x0047;
		break;
	case 8:
		$letter = $x0048;
		break;
	case 9:
		$letter = $x0049;
		break;
	case 10:
		$letter = $x004A;
		break;
	case 11:
		$letter = $x004B;
		break;
	case 12:
		$letter = $x004C;
		break;
	case 13:
		$letter = $x004D;
		break;
	case 14:
		$letter = $x004E;
		break;
	case 15:
		$letter = $x004F;
		break;
	case 16:
		$letter = $x0051;
		break;
	case 17:
		$letter = $x0052;
		break;
	case 18:
		$letter = $x0053;
		break;
	case 19:
		$letter = $x0054;
		break;
	case 20:
		$letter = $x0055;
		break;
	case 21:
		$letter = $x0056;
		break;
	case 22:
		$letter = $x0057;
		break;
	case 23:
		$letter = $x0058;
		break;
	case 24:
		$letter = $x0059;
		break;
	case 25:
		$letter = $x005A;
		break;
	default:
		$letter = $x0031;
		break;
}
endif;

if(isset($_GET['scale'])) :
if ($_GET['scale'] > 0 && $_GET['scale'] < 101) {
	$scale = $_GET['scale'];
} else {
	$scale = 1;
}
else :
$scale = 1;
endif;


$x = $scale;
$y = $scale;

$width = $scale * 7;
$height = $scale * 7;

$image = imagecreate($width, $height);

$background = imagecolorallocate($image, 252, 252, 252);
$color = imagecolorallocate($image, 0, 0, 0);

$j = 0;

$x1 = $x;
for ($i = 0; $i < 25; $i++) {
	if ($j > 4) {
		$y = $y + $scale;
		$x1 = $x;
		if ($letter[$i] > .5) {
			imagefilledrectangle($image, $x1, $y, $x1 + $scale, $y + $scale, imagecolorallocate($image, 0, 0, rand(0, 128)));
		} else {
			imagefilledrectangle($image, $x1, $y, $x1 + $scale, $y + $scale, $background);
		}
		$j = 0;
	} else {
		if ($letter[$i] > .5) {
			imagefilledrectangle($image, $x1, $y, $x1 + $scale, $y + $scale, imagecolorallocate($image, 0, 0, rand(0, 128)));
		} else {
			imagefilledrectangle($image, $x1, $y, $x1 + $scale, $y + $scale, $background);
		}
	}
	$j++;
	$x1 = $x1 + $scale;
}

imagepng($image);
imagedestroy($image);
