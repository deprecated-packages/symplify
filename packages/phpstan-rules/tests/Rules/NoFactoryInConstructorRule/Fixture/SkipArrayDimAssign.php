<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;

final class SkipArrayDimAssign
{
    /**
     * @param OutputFormatterInterface[] $outputFormatters
     */
    public function __construct(array $outputFormatters)
    {
        foreach ($outputFormatters as $outputFormatter) {
            $something[$outputFormatter->getName()] = $outputFormatter;
        }
    }
}
