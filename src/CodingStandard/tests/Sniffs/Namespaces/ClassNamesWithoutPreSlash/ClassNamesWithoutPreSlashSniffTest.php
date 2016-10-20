<?php

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\ClassNamesWithoutPreSlash;

use PHPUnit\Framework\TestCase;
use Symplify\CodingStandard\Tests\CodeSnifferRunner;

final class ClassNamesWithoutPreSlashSniffTest extends TestCase
{
    public function testDetection()
    {
        $codeSnifferRunner = new CodeSnifferRunner('SymplifyCodingStandard.Namespaces.ClassNamesWithoutPreSlash');

        $this->assertSame(1, $codeSnifferRunner->getErrorCountInFile(__DIR__.'/wrong.php.inc'));
        $this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__.'/correct.php.inc'));
    }
}
