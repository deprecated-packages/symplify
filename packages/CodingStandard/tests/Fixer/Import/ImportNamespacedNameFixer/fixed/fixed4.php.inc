<?php

namespace SomeNamespace;

use PrefixedNamespace\SameClass as SecondSameClass;
use AnotherNamespace\SameClass;
class SomeClass extends SecondSameClass
{
    public function create(SameClass $someClass)
    {
    }
}
