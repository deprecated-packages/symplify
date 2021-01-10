<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\ValueObject\Option;

final class SkipConstFetchNonAssign
{
    public function run(InputInterface $input)
    {
        $progressBarEnabled = ! (bool) $input->getOption(Option::NO_PROGRESS_BAR);
    }
}
