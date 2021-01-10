<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\ValueObject\Option;

class SkipConcatOnClassConstFetch
{
    public function otherMethod(InputInterface $input)
    {
        $value = $input->getParameterOption('--' . Option::OUTPUT_FORMAT);

        $isConsoleOutput = $value === ConsoleOutputFormatter::NAME;
    }
}
