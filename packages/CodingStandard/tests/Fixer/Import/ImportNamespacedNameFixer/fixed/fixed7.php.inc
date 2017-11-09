<?php

namespace SomeNamespace;

use PrefixedNamespace\SameClass as ThirdSameClass;
use AnotherNamespace\SameClass as SecondSameClass;
use YetAnotherNamespace\SameClass;
class SomeClass extends ThirdSameClass
{
    public function create(SecondSameClass $someClass)
    {
        return new SameClass;
    }
}
