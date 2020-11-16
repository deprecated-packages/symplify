<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\Tests\DirectoryToMarkdownPrinter;

use Iterator;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\RuleDocGenerator\DirectoryToMarkdownPrinter;
use Symplify\RuleDocGenerator\HttpKernel\RuleDocGeneratorKernel;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DirectoryToMarkdownPrinterTest extends AbstractKernelTestCase
{
    /**
     * @var DirectoryToMarkdownPrinter
     */
    private $directoryToMarkdownPrinter;

    protected function setUp(): void
    {
        $this->bootKernel(RuleDocGeneratorKernel::class);
        $this->directoryToMarkdownPrinter = self::$container->get(DirectoryToMarkdownPrinter::class);
    }

    /**
     * @dataProvider provideDataPHPStan()
     * @dataProvider provideDataPHPCSFixer()
     * @dataProvider provideDataRector()
     */
    public function test(string $directory, string $expectedFile): void
    {
        $fileContent = $this->directoryToMarkdownPrinter->print([$directory]);

        $expectedFileInfo = new SmartFileInfo($expectedFile);
        StaticFixtureUpdater::updateExpectedFixtureContent($fileContent, $expectedFileInfo);

        $directoryFileInfo = new SmartFileInfo($directory);
        $this->assertStringEqualsFile($expectedFile, $fileContent, $directoryFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideDataPHPStan(): Iterator
    {
        yield [__DIR__ . '/Fixture/PHPStan/Standard', __DIR__ . '/Expected/phpstan/phpstan_content.md'];
        yield [
            __DIR__ . '/Fixture/PHPStan/Configurable',
            __DIR__ . '/Expected/phpstan/configurable_phpstan_content.md',
        ];
    }

    public function provideDataPHPCSFixer(): Iterator
    {
        yield [__DIR__ . '/Fixture/PHPCSFixer/Standard', __DIR__ . '/Expected/php-cs-fixer/phpcsfixer_content.md'];
        yield [
            __DIR__ . '/Fixture/PHPCSFixer/Configurable',
            __DIR__ . '/Expected/php-cs-fixer/configurable_phpcsfixer_content.md',
        ];
    }

    public function provideDataRector(): Iterator
    {
        yield [__DIR__ . '/Fixture/Rector/Standard', __DIR__ . '/Expected/rector/rector_content.md'];
        yield [__DIR__ . '/Fixture/Rector/Configurable', __DIR__ . '/Expected/rector/configurable_rector_content.md'];
        yield [
            __DIR__ . '/Fixture/Rector/ComposerJsonAware',
            __DIR__ . '/Expected/rector/composer_json_aware_rector_content.md',
        ];
    }
}
