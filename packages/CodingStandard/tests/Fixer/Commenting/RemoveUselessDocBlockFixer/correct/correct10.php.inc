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
     * @author Tobias Schultze <http://tobion.de>
     * @return File
     */
    public function createFromFileInfo(SplFileInfo $fileInfo): AbstractFile
    {
    }
}
