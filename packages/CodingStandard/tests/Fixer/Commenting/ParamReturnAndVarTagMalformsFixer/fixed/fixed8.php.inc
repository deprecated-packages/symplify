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
