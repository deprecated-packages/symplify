<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\YamlToPhp;

use Iterator;
use Nette\Utils\FileSystem;
use ReflectionClass;
use Symplify\ConfigTransformer\Converter\YamlToPhpConverter;
use Symplify\ConfigTransformer\Tests\Converter\ConfigFormatConverter\AbstractConfigFormatConverterTest;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class YamlToPhpTest extends AbstractConfigFormatConverterTest
{
    /**
     * @dataProvider provideDataForRouting()
     */
    public function testRouting(SmartFileInfo $fileInfo): void
    {
        $this->doTestOutput($fileInfo, true);
    }

    public function testIsRouteYaml(): void
    {
        $class = new ReflectionClass(YamlToPhpConverter::class);
        $instance = $class->newInstanceWithoutConstructor();
        $method = $class->getMethod('isRouteYaml');
        $method->setAccessible(true);
        $call = fn (string $path) => $method->invokeArgs($instance, [$path]);

        foreach ([
            'my_app/config/routes.yaml' => true,
            'my_app/config/routing.yaml' => true,
            'my_app/config/routes/my_packages.yaml' => true,
            'my_app/config/routes/prod/some_prod_route.yaml' => true,
            'my_app/config/packages/routing.yaml' => false
        ] as $case => $expected) {
            $res = $call($case);
            $this->assertEquals($expected, $res);
        }
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
     * @dataProvider provideDataWithPhpImported()
     */
    public function testNormal(SmartFileInfo $fixtureFileInfo): void
    {
        // for imports
        $temporaryPath = StaticFixtureSplitter::getTemporaryPath();
        $this->smartFileSystem->mirror(__DIR__ . '/Fixture/normal', $temporaryPath);
//        require_once $temporaryPath . '/another_dir/SomeClass.php.inc';

        // for the "resource: items/"
        FileSystem::createDir($temporaryPath . '/items');

        // for the "resource: packages/" and assetic import
        FileSystem::copy(__DIR__ . '/Fixture/normal/import_assetic/packages', $temporaryPath . '/packages');

        // for the "resource: directory-with-php/" and PHP config import
        FileSystem::copy(
            __DIR__ . '/Fixture/skip-imported-php/directory-with-php',
            $temporaryPath . '/directory-with-php'
        );

        FileSystem::copy(
            __DIR__ . '/Fixture/normal/directory-with-unquoted-strings',
            $temporaryPath . '/directory-with-unquoted-strings'
        );

        $this->doTestOutput($fixtureFileInfo);
    }

    /**
     * @dataProvider provideDataWithDirectory()
     */
    public function testSpecialCaseWithDirectory(SmartFileInfo $fileInfo): void
    {
        $this->doTestOutputWithExtraDirectory($fileInfo, __DIR__ . '/Fixture/nested');
    }

    /**
     * @dataProvider provideDataEcs()
     * @dataProvider provideDataExtension()
     */
    public function testEcs(SmartFileInfo $fileInfo): void
    {
        $this->doTestOutputWithExtraDirectory($fileInfo, $fileInfo->getPath());
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideDataEcs(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/ecs', '*');
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

        $this->doTestOutput($fileInfo);
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
    public function provideDataWithPhpImported(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/skip-imported-php', '*.yaml');
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideDataExtension(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture/extension', '*.yaml');
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

    private function doTestOutputWithExtraDirectory(SmartFileInfo $fixtureFileInfo, string $extraDirectory): void
    {
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

        $this->doTestFileInfo($inputFileInfo, $inputAndExpected->getExpected(), $fixtureFileInfo);
    }
}
