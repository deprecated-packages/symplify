<?php

namespace SomeNamespace;

use PrefixedNamespace\SameClass;
use AnotherNamespace\SameClass as SecondClass;
class SomeClass extends SameClass
{
    public function create(SecondClass $someClass)
    {
    }
}
