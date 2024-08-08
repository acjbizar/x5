<?php
declare(strict_types=1);

namespace Acj\X5;

use SVG\Nodes\Shapes\SVGRect;
use SVG\Nodes\Structures\SVGGroup;
use SVG\Nodes\Structures\SVGSymbol;
use SVG\Nodes\Structures\SVGUse;
use SVG\SVG;
use SVG\Writing\SVGWriter;

class X5Vector extends X5
{
    public $doc;
    public string $extension = 'svg';
    private SVG $im;

    public function __toString(): string
    {
        $this->_createImage();
        $this->_drawChar(1);

        $writer = new SVGWriter(false);
        $writer->writeNode($this->doc);

        return $writer->getString();
    }

    protected function _createImage(): void
    {
        $width = pow(5, $this->getPower()) + ($this->getMargin() * pow(5, $this->getPower() - 1)) + $this->getX() - 1;
        $height = $width;

        $this->im = new SVG($width, $height);
        $this->doc = $this->im->getDocument();
        if(!empty($this->color)) $this->doc->setAttribute('fill', $this->getColor('hex'));

        if($this->isTransparent()) {
            $this->filename = str_replace('[t]', '-t', $this->filename);
        } else {
            $this->doc->addChild( (new SVGRect(0, 0, '100%', '100%'))->setAttribute('fill', '#' . dechex($this->getBgcolor())) );
            $this->filename = str_replace('[t]', '', $this->filename);
        }
    }

    protected function _drawChar($n = 1): void
    {
        $i = 0;
        $margin = $this->margin;
        $l = $this->_populateGlyph($n);
        $id = 'n' . $this->power . '-' . $n;
        $href = '#n' . $this->power . '-' . ($n - 1);

        if($n === 1) {
            $g = $n == $this->power ? (new SVGGroup())->setAttribute('id', $id) : (new SVGSymbol())->setAttribute('id', $id);

            for($row = 1; $row <= 5; ++$row)
            {
                for($col = 1; $col <= 5; ++$col)
                {
                    if(isset($l[$i]) && $l[$i] === 1)
                    {
                        //$this->_drawRectangle($this->x, $this->y, 1, 1, $this->color);
                        $g->addChild(new SVGRect($this->x, $this->y, 1, 1));
                    }

                    ++$i;
                    $this->x += 1;
                }

                $this->x -= 5;
                $this->y += 1;
            }

            $this->y -= 5;
            //$this->x += $margin;
            $this->doc->addChild($g);

            $this->_drawChar($n + 1);
        } elseif($n <= $this->power) {
            $g = $n == $this->power ? (new SVGGroup())->setAttribute('id', $id)->setAttribute('transform', 'translate(-' . (9 * ($n - 1)) . ',-' . (9 * ($n - 1)) . ')') : (new SVGSymbol())->setAttribute('id', $id);

            for($row = 1; $row <= 5; ++$row)
            {
                for($col = 1; $col <= 5; ++$col)
                {
                    if(isset($l[$i]) && $l[$i] === 1)
                    {
                        if($this->borders) {
                            $border_length = pow(5, $n - 1) + ($margin * pow(5, $n - 2));

                            // Left border.
                            if($col === 1 or (isset($l[$i - 1]) and $l[$i - 1] !== 1)) {
                                $g->addChild((new SVGRect($this->x- 2 + 9 * ($n - 1), $this->y - 2 + 9 * ($n - 1), 1, $border_length + 1)));
                            }

                            // Right border.
                            if($col === 5 or (isset($l[$i + 1]) and $l[$i + 1] !== 1)) {
                                $g->addChild(new SVGRect($this->x - 2 + 9 * ($n - 1) + $border_length, $this->y - 2 + 9 * ($n - 1), 1, $border_length));
                            }

                            // Top border.
                            if($row === 1 or (isset($l[$i - 5]) and $l[$i - 5] !== 1)) {
                                $g->addChild(new SVGRect($this->x - 2 + 9 * ($n - 1), $this->y - 2 + 9 * ($n - 1), $border_length, 1));
                            }

                            // Bottom border.
                            if($row === 5 or (isset($l[$i + 5]) and $l[$i + 5] !== 1)) {
                                $g->addChild(new SVGRect($this->x - 2 + 9 * ($n - 1), $this->y - 2 + 9 * ($n - 1) + $border_length, $border_length + 1, 1));
                            }
                        }

                        //$this->_drawChar($n + 1);
                        $g->addChild((new SVGUse())->setAttribute('href', $href)->setAttribute('x', $this->x)->setAttribute('y', $this->y));
                    }

                    ++$i;
                    $this->x += pow(5, $n - 1) + ($margin * pow(5, $n - 2));
                }

                $this->x -= pow(5, $n) + ($margin * pow(5, $n - 1));
                $this->y += pow(5, $n - 1) + ($margin * pow(5, $n - 2));
            }

            $this->y -= pow(5, $n) + ($margin * pow(5, $n - 1));

            $this->doc->addChild($g);

            $this->_drawChar($n + 1);
        }
    }

    protected function _drawRectangle($x = 0, $y = 0, $width = 1, $height = 1, $color = 0x000000): void
    {
        //$this->doc->addChild( (new SVGRect($x, $y, $width, $height))->setAttribute('fill', '#' . str_pad(dechex($color), 6, '0', STR_PAD_LEFT)) );
        $this->doc->addChild( (new SVGRect($x, $y, $width, $height)) );
    }

    protected function _populateGlyph($n = 1): array
    {
        switch($this->key):
            case 'x5':
                $m = ($this->power - $n + 1) % 3;

                $glyphs = [
                    $this->chars[0x4E], // N
                    $this->chars[0x58], // X
                    $this->chars[0x35], // 5
                ];

                return $glyphs[$m];
                break;
            default:
                return parent::_populateGlyph($n);
        endswitch;
    }

    public function parse(): void
    {
        $this->_createImage();
        $this->_drawChar(1);

        // Prevent caching for characters that may be different every time.
        if($this->key === 'r' || $this->key === 'rand' || $this->key === 'random')
        {
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }

        header('Content-Type: image/svg+xml');

        $writer = new SVGWriter(true);
        $writer->writeNode($this->doc);

        echo $writer->getString();
    }

    public function save($filename = null): void
    {
        $this->_createImage();
        $this->_drawChar(1);
        $path = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR;

        if(empty($filename)) {
            $filename = str_replace(['[power]', '[extension]'], [strval($this->power), $this->extension], $this->filename);
        }

        $writer = new SVGWriter(true);
        $writer->writeNode($this->doc);

        file_put_contents($path . $filename, $writer->getString());
    }
}
