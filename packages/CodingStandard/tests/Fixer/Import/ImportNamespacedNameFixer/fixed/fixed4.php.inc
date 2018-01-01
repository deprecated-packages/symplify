<?php

namespace SomeNamespace;

use AnotherNamespace\SameClass;
use PrefixedNamespace\SameClass as PrefixedNamespaceSameClass;
class SomeClass extends PrefixedNamespaceSameClass
{
    public function create(SameClass $someClass)
    {
    }
}
