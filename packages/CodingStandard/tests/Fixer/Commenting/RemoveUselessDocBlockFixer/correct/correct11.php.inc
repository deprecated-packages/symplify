<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param string $expected
     * @param string|null $input
     */
    protected function doTest($expected, $input = null, ?SplFileInfo $file = null): void
    {
    }

    /**
     * @return DateTimeInterface|string|null
     */
    public function offsetGet($offset)
    {
    }

    /**
     * @return File
     */
    public function createFromFileInfo(SplFileInfo $fileInfo): AbstractFile
    {
    }
}
