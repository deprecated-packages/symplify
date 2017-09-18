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

    /**
     * @var ILoader
     */
    private $loader;

    /**
     * @var ContainerInterface
     */
    private static $cachedContainer;

    /**
     * @var ContainerInterface
     */
    private static $smartContainer;

    public function __construct(SplFileInfo $firstFile, SplFileInfo $secondFile, SomeClassInterface $someClass)
    {
    }
}
