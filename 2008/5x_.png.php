<?php

$letter = array(0,1,1,1,0,1,0,0,0,1,1,1,1,1,1,1,0,0,0,1,1,0,0,0,1);

header("Content-type: image/png");

$width=256;
$height=256;

$image = imagecreate($width, $height);

$colorGrey=imagecolorallocate($image, 192, 192, 192);


$background = imagecolorallocate($image, 255, 255, 255);
$color = imagecolorallocate($image, 0, 0, 0);

// Create grid
for ($i=1; $i<11; $i++){
imageline($image, $i*25, 0, $i*25, 250, $colorGrey);
imageline($image, 0, $i*25, 250, $i*25, $colorGrey);
}

$x = 0;
$y = 0;
$scale = 10;

$j = 0;

for ($i = 0; $i < 25; $i++) {
	if ($letter[$i] == 1) {
		imagefilledrectangle($image, $x + $i * $scale, $y, $x + $i * $scale + $scale, $y + $scale, imagecolorallocate($image, 0, 0, rand(0, 255)));
	} else {
		imagefilledrectangle($image, $x + $i * $scale, $y, $x + $i * $scale + $scale, $y + $scale, $background);
	}
}

imagepng($image);
imagedestroy($image);
