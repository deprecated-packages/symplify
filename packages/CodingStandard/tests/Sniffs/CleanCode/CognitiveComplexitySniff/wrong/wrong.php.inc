<?php

# inspired by https://www.sonarsource.com/docs/CognitiveComplexity.pdf

namespace SomeOtherNamespace;

class SomeClass
{
    public function someFunction($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { } // +3 (nesting=2)
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { } // +2 (nesting=1)
        }
    }
}

// should be total of 9