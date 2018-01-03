<?php

namespace SomeNamespace;

use SomeNode\AnotherClass;
class SomeClass
{
    public function someMethod()
    {
        /** @var AnotherClass $value */
        $value = \stdClass::class;
    }
}
