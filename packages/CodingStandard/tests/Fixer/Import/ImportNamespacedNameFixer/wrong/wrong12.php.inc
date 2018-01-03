<?php

namespace SomeNamespace;

class SomeClass
{
    public function someMethod()
    {
        /** @var SomeNode\SomeClass $value */
        $value = \stdClass::class;
    }
}
