<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
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
        $fileInfo = new SmartFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $errorsAndFileDiffs = $this->fixerFileProcessor->processFile($fileInfo);

        $errors = array_filter($errorsAndFileDiffs, fn (object $object) => $object instanceof CodingStandardError);
        $this->assertCount(0, $errors);

        $fileDiffs = array_filter($errorsAndFileDiffs, fn (object $object) => $object instanceof FileDiff);
        $this->assertCount(1, $fileDiffs);
    }
}
