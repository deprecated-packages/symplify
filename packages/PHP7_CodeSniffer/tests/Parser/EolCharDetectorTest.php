<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;

final class EolCharDetectorTest extends TestCase
{
    public function testDetect()
    {
        $eolCharDetector = new EolCharDetector();

        $this->assertSame(
            PHP_EOL,
            $eolCharDetector->detectForContent('Hi\n')
        );

        $this->assertSame(
            PHP_EOL,
            $eolCharDetector->detectForFilePath(__DIR__.'/EolCharDetectorSource/SomeFile.php')
        );
    }
}
