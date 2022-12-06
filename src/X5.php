<?php
declare(strict_types=1);

namespace Acj\X5;

class X5
{
    private array $chars;

    public function __construct()
    {
        $this->chars = include dirname(__FILE__, 2) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'chars.php';
    }

    public function dumpChars()
    {
        return $this->chars;
    }
}