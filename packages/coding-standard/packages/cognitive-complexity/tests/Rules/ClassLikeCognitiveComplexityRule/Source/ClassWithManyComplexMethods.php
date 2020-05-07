<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule\Source;

final class ClassWithManyComplexMethods
{
    public function someFunction($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { // +3 (nesting=2)
                    }
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { // +2 (nesting=1)
            }
        }
    }

    public function someFunction2($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { // +3 (nesting=2)
                    }
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { // +2 (nesting=1)
            }
        }
    }

    public function someFunction3($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { // +3 (nesting=2)
                    }
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { // +2 (nesting=1)
            }
        }
    }

    public function someFunction4($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { // +3 (nesting=2)
                    }
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { // +2 (nesting=1)
            }
        }
    }

    public function someFunction5($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { // +3 (nesting=2)
                    }
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { // +2 (nesting=1)
            }
        }
    }

    public function someFunction6($var)
    {
        try {
            if (true) { // +1
                for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                    while (true) { // +3 (nesting=2)
                    }
                }
            }
        } catch (\Exception | \Exception $exception) { // +1
            if (true) { // +2 (nesting=1)
            }
        }
    }
}
