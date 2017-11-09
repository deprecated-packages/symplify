<?php

namespace SomeNamespace;

use PrefixedNamespace\SameClass as PrefixedNamespaceSameClass;
use AnotherNamespace\SameClass;
class SomeClass extends PrefixedNamespaceSameClass
{
    public function create(SameClass $someClass)
    {
    }
}
