<?php

class SomeClass
{
    /**
     * @var string $property
     */
    private $property;

    /**
     * @var $anotherProperty \PhpMyAdmin\SqlParser\Statements\CreateStatement
     */
    private $anotherProperty;

    /**
     * @var $this
     */
    private $self;
}

/** @var int $previousTokenPointer */
$previousTokenPointer = 5;

?>
-----
<?php

class SomeClass
{
    /**
     * @var string
     */
    private $property;

    /**
     * @var \PhpMyAdmin\SqlParser\Statements\CreateStatement
     */
    private $anotherProperty;

    /**
     * @var self
     */
    private $self;
}

/** @var int $previousTokenPointer */
$previousTokenPointer = 5;

?>
