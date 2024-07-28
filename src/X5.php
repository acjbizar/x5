<?php
declare(strict_types=1);

namespace Acj\X5;

use GdImage;

const MIN_POWER = 1;
const MAX_POWER = 5;
const MODIFIER_NONE = 0x0;
const MODIFIER_BORDERLESS = 0x1;
const MODIFIER_STRIPES = 0x2;
const MODIFIER_LINES = 0x3;
const MODIFIER_SQUARES = 0x4;
const MODIFIER_CIRCLES = 0x5;
const MODIFIER_6 = 0x6;
const MODIFIER_7 = 0x7;
const MODIFIER_8 = 0x8;
const MODIFIER_9 = 0x9;
const MODIFIER_A = 0xA;
const MODIFIER_B = 0xB;
const MODIFIER_C = 0xC;
const MODIFIER_D = 0xD;
const MODIFIER_E = 0xE;
const MODIFIER_F = 0xF;
const DEFAULT_POWER = 3;
const DEFAULT_VALUE = 100;
const COLOR_RED = 0xff0000;
const COLOR_BLUE = 0x0000ff;
const COLOR_BLACK = 0x000000;
const COLOR_WHITE = 0xffffff;

class X5
{
    private array $chars;
    private array $specials;
    private bool $algorithmic = false;
    private array $algorithmics = ['battery', 'blinker', 'custom', 'identicon', 'logo', 'n', 'network', 'r', 'rand', 'random', 'squared-circle', 'toad', 'wifi', 'x5'];
    private array $identifier = [1,1,1,1,1,1,1,0,1,1,0,0,1,0,0,1,0,0,0,1,0,1,1,1,0];
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
    private mixed $key;
    private array $glyph;
    private mixed $input = [1,1,0,1,1,1,0,0,0,1,0,0,0,0,0,1,0,0,0,1,1,1,0,1,1];
    private array $randomGlyph;
    private bool $transparent = true;
    public string $extension = 'png';
    public string $filename = 'x5-n[power]-[code][t].[extension]';
    public int $value = DEFAULT_VALUE;
    public int $modifier = MODIFIER_NONE;

    public function __construct($key = 'random')
    {
        $this->glyph = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
        $this->key = $key;

        $this->chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
        $this->specials = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'specials.php';

        if(isset($this->chars[$key])) {
            $this->glyph = $this->chars[$key];
            $this->filename = str_replace('[code]', 'u' . str_pad(dechex($key), 4, '0', STR_PAD_LEFT), $this->filename);
        } elseif(isset($this->specials[$key])) {
            $this->setColor(COLOR_RED);
            $this->glyph = $this->specials[$key];
            $this->filename = str_replace('[code]', strval($key), $this->filename);
        } elseif(in_array($key, $this->algorithmics)) {
            $this->algorithmic = true;
            $this->setColor(COLOR_BLUE);
            $this->randomGlyph = $this->_populateRandomGlyph();
            $this->filename = str_replace('[code]', $key, $this->filename);
        } else {
            die('Not sure if this should be possible.');
        }
    }

    private function _createImage(): void
    {
        $width = pow(5, $this->power) + ($this->margin * pow(5, $this->power - 1)) + $this->x - 1;
        $height = $width;
        $this->im = imagecreatetruecolor($width, $height);

        if($this->transparent) {
            $transparent = imagecolorallocatealpha($this->im, 0, 0, 0, 127);
            imagesavealpha($this->im, true);
            imagefill($this->im, 0, 0, $transparent);
            $this->filename = str_replace('[t]', '-t', $this->filename);
        } else {
            imagefill($this->im, 0, 0, $this->bgcolor);
            $this->filename = str_replace('[t]', '', $this->filename);
        }
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
                case 'battery':
                    $middle = intval($this->getValue() > 25);
                    $high = intval($this->getValue() > 75);

                    $l = [0,0,0,0,0,1,1,1,1,0,1,$middle,$high,1,1,1,1,1,1,0,0,0,0,0,0];
                    break;
                case 'blinker':
                    $m = $n % 2;

                    $glyphs = [
                        array(0,0,0,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0),
                        array(0,0,0,0,0,0,0,0,0,0,0,1,1,1,0,0,0,0,0,0,0,0,0,0,0),
                    ];

                    $l = $glyphs[$m];
                    break;
                case 'custom':
                    $l = $this->getInput();
                    break;
                case 'identicon':
                    $l = $this->getIdentifier();
                    break;
                case 'n':
                    $l = $this->chars[mb_ord(strval($n))];
                    break;
                case 'network':
                    $middle = intval($this->getValue() > 25);
                    $high = intval($this->getValue() > 75);

                    $l = [0,0,0,0,$high,0,0,0,0,$high,0,0,$middle,0,$high,0,0,$middle,0,$high,1,0,$middle,0,$high];
                    break;

                    break;
                case 'squared-circle':
                    $m = $n % 3;

                    $glyphs = [
                        array(0,0,0,0,0,0,0,1,0,0,0,1,0,1,0,1,1,1,1,1,0,0,0,0,0),
                        array(0,1,1,1,0,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,0,1,1,1,0),
                        array(1,1,1,1,1,1,0,0,0,1,1,0,0,0,1,1,0,0,0,1,1,1,1,1,1),
                    ];

                    $l = $glyphs[$m];
                    break;
                case 'toad':
                    $m = $n % 2;

                    $glyphs = [
                        array(0,0,0,0,0,0,1,1,1,0,1,1,1,0,0,0,0,0,0,0,0,0,0,0,0),
                        array(0,0,1,0,0,1,0,0,1,0,1,0,0,1,0,0,1,0,0,0,0,0,0,0,0)
                    ];

                    $l = $glyphs[$m];
                    break;
                case 'wifi':
                    $middle = intval($this->getValue() > 25);
                    $high = intval($this->getValue() > 75);

                    $l = [$high,$high,$high,$high,$high,0,0,0,0,0,0,$middle,$middle,$middle,0,0,0,0,0,0,0,0,1,0,0];
                    break;

                    break;
                case 'x5':
                    $m = ($this->power - $n) % 3;

                    $glyphs = [
                        $this->chars[0x58], // X
                        $this->chars[0x35], // 5
                        $this->chars[0x4E], // N
                    ];

                    $l = $glyphs[$m];
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
                        switch($this->modifier) {
                            case MODIFIER_SQUARES:
                                $random = imagecolorallocatealpha($this->im, 0, mt_rand(0, 255), 0, 63);
                                imagefilledrectangle($this->im, $this->x - 1, $this->y - 1, $this->x + pow(5, $n - 1) + ($margin * pow(5, $n - 2)) - 2, $this->y + pow(5, $n - 1) + ($margin * pow(5, $n - 2) - 2), $random);
                                //imagefilledellipse($this->im, intval($this->x + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2), intval($this->y + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2, pow(5, $n - 1)  + ($margin * pow(5, $n - 2)), pow(5, $n - 1) + ($margin * pow(5, $n - 2)), $random));
                                //imageellipse($this->im, intval($this->x + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2), intval($this->y + pow(5, $n - 1) / 2 + ($margin * pow(5, $n - 2)) / 2, pow(5, $n - 1)  + ($margin * pow(5, $n - 2)), pow(5, $n - 1) + ($margin * pow(5, $n - 2)), $random));
                                break;
                            default:
                                // Skip.
                        }
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

    private function _populateRandomGlyph(): array
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

    public function getCharsAsString(): mixed
    {
        $keys = array_keys($this->chars);
        $glyphs = array_map('mb_chr', $keys);
        $str = implode('', $glyphs);

        return $str;
    }

    public function getGlyph(): mixed
    {
        return $this->glyph;
    }

    public function getIdentifier(): mixed
    {
        return $this->identifier;
    }

    public function getInput(): mixed
    {
        return $this->input;
    }

    public function getModifier(): int
    {
        return $this->modifier;
    }

    public function getPower(): int
    {
        return $this->power;
    }

    public function getSpecialChar(): mixed
    {
        return $this->specials[0x53] ?? false;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function hexToGlyph($hex): array
    {
        $glyph;

        for ($i = 0; $i < strlen($hex); $i++) {
            if(!empty($hex[$i]) && hexdec($hex[$i]) >= 8) {
                $glyph[] = 1;
            } else {
                $glyph[] = 0;
            }
        }

        return $glyph;
    }

    public function setColor($color): void
    {
        // Turn format "#FFFFFF" into 0xFFFFFF.
        if(is_string($color)) {
            $color = hexdec(ltrim($color, '#'));
        }

        $this->color = $color;
    }

    public function setExtension($extension): void
    {
        $this->extension = $extension;
    }

    public function setIdentifier(mixed $identifier): void
    {
        if(!empty($identifier)) {
            $hash = md5($identifier);
            $color = substr($hash, 0, 6);
            $modifier = substr($hash, 6, 1);
            $char = substr($hash, 7, 25);

            $this->identifier = $this->hexToGlyph($char);
            $this->setColor(hexdec($color));
        }
    }

    public function setInput($input): void
    {
        if(is_string($input))
        {
            $input = str_split($input);
        }

        // Convert values to integers, so we can process them faster later on.
        $input = array_map('intval', $input);

        foreach($this->input as $key => $value) {
            $this->input[$key] = !empty($input[$key]) ? 1 : 0;
        }
    }

    public function setModifier($modifier): void
    {
        $this->modifier = $modifier;

        if($modifier === MODIFIER_BORDERLESS) {
            $this->borders = false;
        }
    }

    public function setPower(int $power): void
    {
        $this->power = $power;

        $this->filename = str_replace('[power]', strval($power), $this->filename);
    }

    public function setTransparent(bool $switch = TRUE): void
    {
        $this->transparent = $switch;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function parse(): void
    {
        $this->_createImage();
        $this->_drawChar($this->power);

        // Prevent caching for characters that may be different every time.
        if($this->key === 'r' || $this->key === 'rand' || $this->key === 'random')
        {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }

        header('Content-Type: image/png');

        switch($this->extension):
            case 'avif':
                imageavif($this->im);
                break;
            case 'gif':
                imagegif($this->im);
                break;
            case 'png':
            default:
                imagepng($this->im);
        endswitch;

        imagepng($this->im);
        imagedestroy($this->im);
    }

    public function save($filename = null): void
    {
        $this->_createImage();
        $this->_drawChar($this->power);
        $path = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR;

        if(empty($filename)) {
            $filename = str_replace(['[power]', '[extension]'], [strval($this->power), $this->extension], $this->filename);
        }

        switch($this->extension):
            case 'avif':
                imageavif($this->im,$path . $filename);
                break;
            case 'gif':
                imagegif($this->im, $path. $filename);
                break;
            case 'webp':
                imagewebp($this->im, $path. $filename);
                break;
            case 'png':
            default:
                imagepng($this->im,$path . $filename);
        endswitch;

        imagedestroy($this->im);
    }
}