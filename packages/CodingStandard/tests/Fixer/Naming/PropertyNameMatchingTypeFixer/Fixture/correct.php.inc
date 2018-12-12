<?php

use PhpParser\Node\Arg;

class SomeClass
{
    /**
     * @var Arg
     */
    public $argNode;

    public function __construct(
        Arg $argNode
    ) {
    }
}
