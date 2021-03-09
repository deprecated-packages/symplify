<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\YamlToPhp;

use Iterator;
use Symplify\ConfigTransformer\Configuration\Configuration;
use Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\AbstractConfigFormatConverterTest;
use Symplify\ConfigTransformer\ValueObject\Format;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class YamlToPhpTest extends AbstractConfigFormatConverterTest
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var Configuration $configuration */
        $configuration = $this->getService(Configuration::class);
        $configuration->changeSymfonyVersion(3.4);
    }

    /**
     * @dataProvider provideDataForRouting()
     */
    public function testRouting(SmartFileInfo $fileInfo): void
    {
        $this->doTestOutput($fileInfo, Format::YAML, Format::PHP);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideDataForRouting(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/routing', '*.yaml');
    }

    /**
     * @dataProvider provideData()
     */
    public function testNormal(SmartFileInfo $fixtureFileInfo): void
    {
        // for imports
        $temporaryPath = StaticFixtureSplitter::getTemporaryPath();
        $this->smartFileSystem->mirror(__DIR__ . '/Fixture/normal', $temporaryPath);
        require_once $temporaryPath . '/another_dir/SomeClass.php.inc';

        $this->doTestOutput($fixtureFileInfo, Format::YAML, Format::PHP);
    }

    /**
     * @dataProvider provideDataWithDirectory()
     */
    public function testSpecialCaseWithDirectory(SmartFileInfo $fileInfo): void
    {
        $this->doTestOutputWithExtraDirectory($fileInfo, __DIR__ . '/Fixture/nested', Format::YAML, Format::PHP);
    }

    /**
     * @dataProvider provideDataEcs()
     */
    public function testEcs(SmartFileInfo $fileInfo): void
    {
        $this->doTestOutputWithExtraDirectory($fileInfo, __DIR__ . '/Fixture/ecs', Format::YAML, Format::PHP);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideDataEcs(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/ecs', '*.yaml');
    }

    /**
     * @source https://github.com/symfony/maker-bundle/pull/604
     * @dataProvider provideDataMakerBundle()
     */
    public function testMakerBundle(SmartFileInfo $fileInfo): void
    {
        // needed for all the included
        $temporaryPath = StaticFixtureSplitter::getTemporaryPath();
        $this->smartFileSystem->dumpFile(
            $temporaryPath . '/../src/SomeClass.php',
            '<?php namespace App { class SomeClass {} }'
        );
        require_once $temporaryPath . '/../src/SomeClass.php';

        $this->smartFileSystem->mkdir($temporaryPath . '/../src/Controller');
        $this->smartFileSystem->mkdir($temporaryPath . '/../src/Domain');

        $this->doTestOutput($fileInfo, Format::YAML, Format::PHP);
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/normal', '*.yaml');
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideDataWithDirectory(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/nested', '*.yaml');
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideDataMakerBundle(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/maker-bundle', '*.yaml');
    }

    private function doTestOutputWithExtraDirectory(
        SmartFileInfo $fixtureFileInfo,
        $extraDirectory,
        string $inputFormat,
        string $outputFormat
    ): void {
        $this->configuration->changeInputFormat($inputFormat);
        $this->configuration->changeOutputFormat($outputFormat);

        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $temporaryPath = StaticFixtureSplitter::getTemporaryPath();

        // copy /src to temp directory, so Symfony FileLocator knows about it
        $this->smartFileSystem->mirror($extraDirectory, $temporaryPath, null, [
            'override' => true,
        ]);

        $fileTemporaryPath = $temporaryPath . '/' . $fixtureFileInfo->getRelativeFilePathFromDirectory($extraDirectory);
        $this->smartFileSystem->dumpFile($fileTemporaryPath, $inputAndExpected->getInput());

        // require class to autoload it
        $expectedFilePath = $temporaryPath . '/src/SomeClass.php';
        $this->assertFileExists($expectedFilePath);

        require_once $expectedFilePath;

        $inputFileInfo = new SmartFileInfo($fileTemporaryPath);

        $this->doTestFileInfo(
            $inputFileInfo,
            $inputAndExpected->getExpected(),
            $fixtureFileInfo,
            $inputFormat,
            $outputFormat
        );
    }
}
