<?php

namespace SomeNamespace;

use AnotherNamespace\AnotherClass;
use PrefixedNamespace\ParentClass;
class SomeClass extends ParentClass
{
    public function create(AnotherClass $someClass)
    {
    }
}
