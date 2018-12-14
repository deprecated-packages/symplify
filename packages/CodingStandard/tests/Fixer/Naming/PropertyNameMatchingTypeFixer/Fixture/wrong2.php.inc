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

    /**
     * @var callable|IPresenter
     */
    private $presenter;

    public function __construct(
        SplFileInfo $firstFile,
        SplFileInfo $secondFile,
        SomeClassInterface $class
    ) {
    }
}

?>
-----
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

    /**
     * @var callable|IPresenter
     */
    private $presenter;

    public function __construct(
        SplFileInfo $firstFile,
        SplFileInfo $secondFile,
        SomeClassInterface $someClass
    ) {
    }
}

?>
