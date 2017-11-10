<?php

namespace SomeNamespace;

use PrefixedNamespace\ParentClass;
class SomeClass extends ParentClass
{
    public function create()
    {
        return new ParentClass;
    }
}
