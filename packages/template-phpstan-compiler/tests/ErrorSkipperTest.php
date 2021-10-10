<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Tests;

use PHPStan\Analyser\Error;
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
        $ruleError = new Error('Some message', 'some_file.php');

        $nonFilteredErrors = $this->errorSkipper->skipErrors([$ruleError], []);
        $this->assertSame([$ruleError], $nonFilteredErrors);

        $filteredErrors = $this->errorSkipper->skipErrors([$ruleError], ['#some#i']);
        $this->assertEmpty($filteredErrors);
    }
}
