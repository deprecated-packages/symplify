<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Tests\ErrorFormatter;

use Iterator;
use PHPStan\Analyser\Error;
use PHPStan\Command\AnalysisResult;
use PHPUnit\Framework\TestCase;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;
use Symplify\PHPStanExtensions\ErrorFormatter\SymplifyErrorFormatter;
use Symplify\PHPStanExtensions\Tests\ErrorFormatter\Source\DummyOutput;

final class SymplifyErrorFormatterTest extends TestCase
{
    /**
     * @var SymplifyErrorFormatter
     */
    private $symplifyErrorFormatter;

    private DummyOutput $dummyOutput;

    protected function setUp(): void
    {
        $phpStanContainerFactory = new PHPStanContainerFactory();
        $container = $phpStanContainerFactory->createContainer([__DIR__ . '/../../config/config.neon']);

        $this->symplifyErrorFormatter = $container->getByType(SymplifyErrorFormatter::class);
        $this->dummyOutput = new DummyOutput();
    }

    /**
     * @dataProvider provideData()
     * @param Error[] $errors
     */
    public function testSingleFileSingleError(array $errors, string $expectedFile): void
    {
        $analysisResult = new AnalysisResult($errors, [], [], [], false, null, false);

        $this->symplifyErrorFormatter->formatErrors($analysisResult, $this->dummyOutput);

        $bufferedContent = $this->dummyOutput->getBufferedContent();
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
}
