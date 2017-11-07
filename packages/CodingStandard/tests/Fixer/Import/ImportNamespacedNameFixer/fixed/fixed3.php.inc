<?php

namespace SomeNamespace;

use PrefixedNamespace\ParentClass;
use AnotherNamespace\AnotherClass;
class SomeClass extends ParentClass
{
    public function create(AnotherClass $someClass)
    {
    }
}
