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

    public function __construct()
    {
        $this->chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
    }

    private function _createImage(): void
    {
        $this->im = imagecreatetruecolor(1024, 1024);
    }

    private function _drawChar(): void
    {
        $f00 = imagecolorallocate($this->im, 255, 0, 0);
        $l = $this->getChar();
        $i = 0;
        $x = 10;
        $y = 10;

        for($row = 1; $row <= 5; ++$row) {
            for ($col = 1; $col <= 5; ++$col) {
                if (isset($l[$i]) && $l[$i] === 1) {
                    imagefilledrectangle($this->im, $x, $y, $x, $y, $f00);
                }

                ++$i;
                $x += 1;
            }

            $x -= 5;
            $y += 1;
        }
    }

    public function dumpChars()
    {
        return $this->chars;
    }

    public function getChar(): array
    {
        return $this->chars[0x58];
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
        $this->_drawChar();
        header('Content-Type: image/png');

        imagepng($this->im);
        imagedestroy($this->im);
    }
}