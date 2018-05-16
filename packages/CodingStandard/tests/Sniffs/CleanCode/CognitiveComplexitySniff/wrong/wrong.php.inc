<?php

namespace SomeOtherNamespace;

class SomeClass
{
    public function someFunction($var)
    {
        if (false) { // +1
            while (true) { // +1 for while, +1 for lvl 1 nesting

            }

        } elseif (true) {

        } else {

        }
    }
}
