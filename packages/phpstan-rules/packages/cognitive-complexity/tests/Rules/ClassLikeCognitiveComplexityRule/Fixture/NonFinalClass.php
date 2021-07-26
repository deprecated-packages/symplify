<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule\Fixture;

use Symfony\Component\Console\Command\Command;

class NonFinalClass extends Command // +10 (non-final class) +25 (extending another class)
{
    public function someFunction($var)
    {
        if (true) { // +1
            for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
                while (true) { // +3 (nesting=2)
                }
            }
        }
    }

    public function someFunction3($var)
    {
        if (true) { // +1
            for ($i = 0; $i < 10; $i++) { // +2 (nesting=1)
            }
        }
    }
}
