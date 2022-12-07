<?php
declare(strict_types=1);

namespace Acj\X5;

use GdImage;

const MAX_POWER = 5;

class X5
{
    private array $chars;
    private GdImage $im;
    private int $power = 3;
    private int $x = 9;
    private int $y = 9;
    private int $width = 512;
    private int $height = 512;
    private int $margin = 3;

    public function __construct()
    {
        $this->chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
    }

    private function _createImage(): void
    {
        $width = pow(5, $this->power) + ($this->margin * pow(5, $this->power - 1)) + $this->x - 1;
        $height = $width;
        $this->im = imagecreatetruecolor($width, $height);
        imagefill($this->im, 0, 0, 0x000000);
    }

    private function _drawChar($n = 1): void
    {
        $f00 = imagecolorallocate($this->im, 255, 0, 0);
        $l = $this->getChar();
        $i = 0;
        $margin = $this->margin;

        if($n === 1) {
            for($row = 1; $row <= 5; ++$row)
            {
                for($col = 1; $col <= 5; ++$col)
                {
                    if(isset($l[$i]) && $l[$i] === 1)
                    {
                        imagefilledrectangle($this->im, $this->x, $this->y, $this->x, $this->y, $f00);
                    }

                    ++$i;
                    $this->x += 1;
                }

                $this->x -= 5;
                $this->y += 1;
            }

            $this->y -= 5;
            //$this->x += $margin;
        } else {
            for($row = 1; $row <= 5; ++$row)
            {
                for($col = 1; $col <= 5; ++$col)
                {
                    if(isset($l[$i]) && $l[$i] === 1)
                    {
                        if($n === 2 && $col === 1) {
                            imagefilledrectangle($this->im, $this->x - 2, $this->y - 2, $this->x - 2, $this->y + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, mt_rand(0, 0xffffff));
                        }

                        $this->_drawChar($n - 1);
                    }
                    else
                    {
                        /*
                        $random = imagecolorallocatealpha($img, 0, mt_rand(0, 255), 0, 63);
                        //imagefilledrectangle($img, $this->x, $this->y + pow(5, $n), $this->x + pow(5, $n), $this->y, $random);
                        imagefilledellipse($img, $this->x + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2, $this->y + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2, pow(5, $n - 1)  + ($margin * pow(5, $n - 2)), pow(5, $n - 1) + ($margin * pow(5, $n - 2)), $random);
                        imageellipse($img, $this->x + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2, $this->y + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2, pow(5, $n - 1)  + ($margin * pow(5, $n - 2)), pow(5, $n - 1) + ($margin * pow(5, $n - 2)), $random);
                        */
                    }

                    ++$i;
                    $this->x += pow(5, $n - 1) + ($margin * pow(5, $n - 2));
                }

                $this->x -= pow(5, $n) + ($margin * pow(5, $n - 1));
                $this->y += pow(5, $n - 1) + ($margin * pow(5, $n - 2));
            }

            $this->y -= pow(5, $n) + ($margin * pow(5, $n - 1));
        }
    }

    private function _drawPowerChar()
    {
        //
    }

    public function dumpChars()
    {
        return $this->chars;
    }

    public function getChar(): array
    {
        return $this->chars[0x53];
    }

    public function getPower(): int
    {
        return $this->power;
    }

    public function setPower(int $power): void
    {
        $this->power = $power;
    }

    public function parse(): void
    {
        $this->_createImage();
        $this->_drawChar($this->power);
        header('Content-Type: image/png');

        imagepng($this->im);
        imagedestroy($this->im);
    }
}