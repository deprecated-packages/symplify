<?php

namespace SomeNamespace;

class SomeClass
{
    public function someMethod()
    {
        /** @var SomeNode\AnotherClass $value */
        $value = \stdClass::class;
    }
}
