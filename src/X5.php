<?php
declare(strict_types=1);

namespace Acj\X5;

const MAX_POWER = 5;

class X5
{
    private array $chars;
    private int $power = 3;

    public function __construct()
    {
        $this->chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
    }

    private function _createImage()
    {
        $this->im = imagecreatetruecolor(1024, 1024);
    }

    public function dumpChars()
    {
        return $this->chars;
    }

    public function getPower(): int
    {
        return $this->power;
    }

    public function setPower(int $power): void
    {
        $this->power = $power;
    }
}