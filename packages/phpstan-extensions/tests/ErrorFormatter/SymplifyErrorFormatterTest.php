<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\ErrorFormatter;

use Iterator;
use PHPStan\Testing\ErrorFormatterTestCase;
use Symplify\PHPStanExtensions\ErrorFormatter\SymplifyErrorFormatter;

/**
 * @see https://github.com/phpstan/phpstan-src/blob/1.8.x/tests/PHPStan/Command/ErrorFormatter/RawErrorFormatterTest.php
 */
final class SymplifyErrorFormatterTest extends ErrorFormatterTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function testFormatErrors(
        string $message,
        int $expectedExitCode,
        int $numFileErrors,
        int $numGenericErrors,
        string $expectedOutputFile,
    ): void {
        $symplifyErrorFormatter = self::getContainer()->getByType(SymplifyErrorFormatter::class);

        $analysisResult = $this->getAnalysisResult($numFileErrors, $numGenericErrors);
        $resultCode = $symplifyErrorFormatter->formatErrors($analysisResult, $this->getOutput(),);

        $this->assertSame($expectedExitCode, $resultCode);

        $this->assertStringMatchesFormatFile($expectedOutputFile, $this->getOutputContent());
    }

    /**
     * @return Iterator<mixed>
     */
    public function provideData(): Iterator
    {
        yield ['Some message', 1, 1, 1, __DIR__ . '/Fixture/expected_single_message_many_files_report.txt'];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../config/config.neon'];
    }
}
