<?php
declare(strict_types=1);

namespace Acj\X5;

use GdImage;

const MIN_POWER = 1;
const MAX_POWER = 5;
const DEFAULT_POWER = 3;
const COLOR_RED = 0xff0000;
const COLOR_BLUE = 0x0000ff;
const COLOR_BLACK = 0x000000;
const COLOR_WHITE = 0xffffff;

class X5
{
    private array $chars;
    private array $specials;
    private bool $algorithmic = false;
    private array $algorithmics = ['logo', 'n', 'r', 'rand', 'random'];
    private GdImage $im;
    private int $power = DEFAULT_POWER;
    private int $x = 9;
    private int $y = 9;
    private int $width = 512;
    private int $height = 512;
    private int $margin = 3;
    private bool $borders = true;
    private int $bgcolor = 0xffffff;
    private int $color = 0x0;
    private string $key;
    private array $glyph;
    private array $randomGlyph;

    public function __construct($key = 'random')
    {
        $this->glyph = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        $this->key = $key;

        $this->chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
        $this->specials = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'specials.php';

        if(isset($this->chars[$key])) {
            $this->glyph = $this->chars[$key];
        } elseif(isset($this->specials[$key])) {
            $this->setColor(COLOR_RED);
            $this->glyph = $this->specials[$key];
        } elseif(in_array($key, $this->algorithmics)) {
            $this->algorithmic = true;
            $this->setColor(COLOR_BLUE);
            $this->randomGlyph = $this->_populateRandomGlyph();
        }
    }

    private function _createImage(): void
    {
        $width = pow(5, $this->power) + ($this->margin * pow(5, $this->power - 1)) + $this->x - 1;
        $height = $width;
        $this->im = imagecreatetruecolor($width, $height);
        imagefill($this->im, 0, 0, $this->bgcolor);
    }

    private function _drawBorders()
    {

    }

    private function _drawChar($n = 1): void
    {
        $i = 0;
        $margin = $this->margin;

        if($this->algorithmic) {
            switch($this->key) {
                case 'n':
                    $l = $this->getGlyph();
                    break;
                default:
                    $l = $this->randomGlyph;
            }
        } else {
            $l = $this->getGlyph();
        }

        if($n === 1) {
            for($row = 1; $row <= 5; ++$row)
            {
                for($col = 1; $col <= 5; ++$col)
                {
                    if(isset($l[$i]) && $l[$i] === 1)
                    {
                        imagefilledrectangle($this->im, $this->x, $this->y, $this->x, $this->y, $this->color);
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
                        if($this->borders) {
                            if($col === 1 or (isset($l[$i - 1]) and $l[$i - 1] !== 1)) {
                                imagefilledrectangle($this->im, $this->x - 2, $this->y - 2, $this->x - 2, $this->y + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->color);
                            }

                            if($col === 5 or (isset($l[$i + 1]) and $l[$i + 1] !== 1)) {
                                imagefilledrectangle($this->im, $this->x + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->y - 2, $this->x + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->y + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->color);
                            }

                            if($row === 1 or (isset($l[$i - 5]) and $l[$i - 5] !== 1)) {
                                imagefilledrectangle($this->im, $this->x - 2, $this->y - 2, $this->x + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->y - 2, $this->color);
                            }

                            if($row === 5 or (isset($l[$i + 5]) and $l[$i + 5] !== 1)) {
                                imagefilledrectangle($this->im, $this->x - 2, $this->y + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->x + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->y + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->color);
                            }
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

    private function _populateRandomGlyph()
    {
        return [mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1),
            mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1),
            mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1),
            mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1),
            mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1), mt_rand(0, 1)];
    }

    public function dumpChars()
    {
        return $this->chars;
    }

    public function getChar($key): mixed
    {
        return $this->chars[$key] ?? false;
    }

    public function getGlyph(): mixed
    {
        return $this->glyph;
    }

    public function getPower(): int
    {
        return $this->power;
    }

    public function getSpecialChar(): mixed
    {
        return $this->specials[0x53] ?? false;
    }

    public function setColor($color): void
    {
        $this->color = $color;
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