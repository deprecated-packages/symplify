<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\ErrorFormatter;

use Iterator;
use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPStan\Testing\PHPStanTestCase;
use Symplify\PHPStanExtensions\ErrorFormatter\SymplifyErrorFormatter;
use Symplify\PHPStanExtensions\Tests\ErrorFormatter\Source\DummyOutput;

final class SymplifyErrorFormatterTest extends PHPStanTestCase
{
    /**
     * @dataProvider provideData()
     * @param Error[] $errors
     */
    public function testSingleFileSingleError(array $errors, string $expectedFile): void
    {
        $analysisResult = new AnalysisResult($errors, [], [], [], false, null, false);

        $symplifyErrorFormatter = self::getContainer()->getByType(SymplifyErrorFormatter::class);
        $dummyOutput = new DummyOutput();

        $symplifyErrorFormatter->formatErrors($analysisResult, $dummyOutput);

        $bufferedContent = $dummyOutput->getBufferedContent();
        $this->assertStringMatchesFormatFile($expectedFile, $bufferedContent);
    }

    public function provideData(): Iterator
    {
        $errors = [new Error('Some message', 'some_file.php', 55)];
        yield [$errors, __DIR__ . '/Fixture/expected_single_error_report.txt'];

        $sameMessageErrors = [
            new Error('The identical message', 'some_file.php'),
            new Error('The identical message', 'another_some_file.php'),
        ];
        yield [$sameMessageErrors, __DIR__ . '/Fixture/expected_single_message_many_files_report.txt'];
    }

    /**
     * @return string[]
     */
    public static function getAdditionalConfigFiles(): array
    {
        return [__DIR__ . '/../../config/config.neon'];
    }
}
