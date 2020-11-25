<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\CognitiveComplexity\Tests\Rules\ClassLikeCognitiveComplexityRule\Fixture;

use Symfony\Component\Console\Command\Command;

final class SimpleCommand extends Command
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
