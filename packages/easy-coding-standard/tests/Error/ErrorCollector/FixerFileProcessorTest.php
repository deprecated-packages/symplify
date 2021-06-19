<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixerFileProcessorTest extends AbstractKernelTestCase
{
    private FixerFileProcessor $fixerFileProcessor;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FixerRunnerSource/phpunit-fixer-config.php']
        );

        $this->fixerFileProcessor = $this->getService(FixerFileProcessor::class);
    }

    public function test(): void
    {
        $configuration = new Configuration();

        $fileInfo = new SmartFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $errorsAndFileDiffs = $this->fixerFileProcessor->processFile($fileInfo, $configuration);

        $errors = $errorsAndFileDiffs[Bridge::CODING_STANDARD_ERRORS] ?? [];
        $this->assertCount(0, $errors);

        $fileDiffs = $errorsAndFileDiffs[Bridge::FILE_DIFFS] ?? [];
        $this->assertCount(1, $fileDiffs);
    }
}
