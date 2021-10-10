<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Tests;

use PHPStan\Rules\RuleErrorBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\TemplatePHPStanCompiler\ErrorSkipper;

final class ErrorSkipperTest extends TestCase
{
    private ErrorSkipper $errorSkipper;

    protected function setUp(): void
    {
        $this->errorSkipper = new ErrorSkipper();
    }

    public function test(): void
    {
        $ruleError = RuleErrorBuilder::message('Some message')
            ->build();

        $nonFilteredErrors = $this->errorSkipper->skipErrors([$ruleError], []);
        $this->assertSame([$ruleError], $nonFilteredErrors);

        $filteredErrors = $this->errorSkipper->skipErrors([$ruleError], ['#some#i']);
        $this->assertEmpty($filteredErrors);
    }
}
