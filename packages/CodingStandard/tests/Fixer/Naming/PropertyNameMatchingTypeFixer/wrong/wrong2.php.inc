<?php

class SomeClass
{
    /**
     * @var string
     */
    private $filenameWithoutDate;

    /**
     * @var SomeClass[]
     */
    private $someClasses;

    public function __construct(SplFileInfo $firstFile, SplFileInfo $secondFile, SomeClassInterface $class)
    {
    }
}
