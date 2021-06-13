<?php

class SomeClass
{
    /**
     * @var string $property
     */
    private $property;

    /**
     * @phpstan-var string $prefixedProperty
     */
    private $prefixedProperty;

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
     * @phpstan-var string
     */
    private $prefixedProperty;

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
