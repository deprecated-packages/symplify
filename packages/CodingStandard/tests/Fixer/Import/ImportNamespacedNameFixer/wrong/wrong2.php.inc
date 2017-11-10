<?php

namespace SomeNamespace;

class SomeClass extends \PrefixedNamespace\ParentClass
{
    public function create()
    {
        return new \PrefixedNamespace\ParentClass;
    }
}
