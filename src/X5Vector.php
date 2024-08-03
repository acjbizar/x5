<?php
declare(strict_types=1);

namespace Acj\X5;

use SVG\Nodes\Shapes\SVGRect;
use SVG\SVG;
use SVG\Writing\SVGWriter;

class X5Vector extends X5
{
    public $doc;
    public string $extension = 'svg';
    private SVG $im;

    protected function _createImage(): void
    {
        $width = pow(5, $this->getPower()) + ($this->getMargin() * pow(5, $this->getPower() - 1)) + $this->getX() - 1;
        $height = $width;

        $this->im = new SVG($width, $height);
        $this->doc = $this->im->getDocument();

        if($this->isTransparent()) {
            $this->filename = str_replace('[t]', '-t', $this->filename);
        } else {
            $this->doc->addChild( (new SVGRect(0, 0, '100%', '100%'))->setAttribute('fill', '#' . dechex($this->getBgcolor())) );
            $this->filename = str_replace('[t]', '', $this->filename);
        }
    }

    protected function _drawRectangle($x = 0, $y = 0, $width = 1, $height = 1, $color = 0x000000): void
    {
        $this->doc->addChild( (new SVGRect($x, $y, $width, $height))->setAttribute('fill', '#' . str_pad(dechex($color), 6, '0', STR_PAD_LEFT)) );
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

        header('Content-Type: image/svg+xml');

        $writer = new SVGWriter(true);
        $writer->writeNode($this->doc);

        echo $writer->getString();
    }

    public function save($filename = null): void
    {
        $this->_createImage();
        $this->_drawChar($this->power);
        $path = dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'dist' . DIRECTORY_SEPARATOR;

        if(empty($filename)) {
            $filename = str_replace(['[power]', '[extension]'], [strval($this->power), $this->extension], $this->filename);
        }

        $writer = new SVGWriter(true);
        $writer->writeNode($this->doc);

        file_put_contents($path . $filename, $writer->getString());
    }
}
