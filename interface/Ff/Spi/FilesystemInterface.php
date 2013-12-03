<?php

namespace Ff\Spi;

interface FilesystemInterface
{
    public function read($path);

    public function write($path, $content, $mode);
}
