<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler;

use Iterator;
use PHPStan\DependencyInjection\Container;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\LattePHPStanCompiler\LatteToPhpCompiler;
use Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\SomeNameControl;
use Symplify\LattePHPStanCompiler\ValueObject\ComponentNameAndType;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;

final class LatteToPhpCompilerTest extends TestCase
{
    private LatteToPhpCompiler $latteToPhpCompiler;

    protected function setUp(): void
    {
        $container = $this->createContainer();
        $this->latteToPhpCompiler = $container->getByType(LatteToPhpCompiler::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fileInfo);
        $phpFileContent = $this->latteToPhpCompiler->compileContent($inputAndExpected->getInput(), [], []);

        // update test fixture if the content has changed
        StaticFixtureUpdater::updateFixtureContent($inputAndExpected->getInput(), $phpFileContent, $fileInfo);

        $this->assertSame($phpFileContent, $inputAndExpected->getExpected());
    }

    /**
     * @dataProvider provideDataWithTypesAndControls()
     *
     * @param VariableAndType[] $variablesAndTypes
     * @param ComponentNameAndType[] $componentNamesAndtTypes
     */
    public function testTypesAndControls(
        string $inputLatteFile,
        array $variablesAndTypes,
        array $componentNamesAndtTypes,
        string $expectedPhpContent
    ): void {
        $smartFileSystem = new SmartFileSystem();

        $inputLatteFileContent = $smartFileSystem->readFile($inputLatteFile);

        $phpFileContent = $this->latteToPhpCompiler->compileContent(
            $inputLatteFileContent,
            $variablesAndTypes,
            $componentNamesAndtTypes
        );

        $this->assertStringMatchesFormatFile($expectedPhpContent, $phpFileContent);
    }

    public function provideDataWithTypesAndControls(): Iterator
    {
        $variablesAndTypes = [new VariableAndType('someName', new StringType())];
        yield [
            __DIR__ . '/FixtureWithTypes/input_file.latte',
            $variablesAndTypes,
            [],
            __DIR__ . '/FixtureWithTypes/expected_compiled.php',
        ];

        $componentNamesAndTypes = [new ComponentNameAndType('someName', new ObjectType(SomeNameControl::class))];

        yield [
            __DIR__ . '/FixtureWithControl/input_file.latte',
            [],
            $componentNamesAndTypes,
            __DIR__ . '/FixtureWithControl/expected_compiled.php',
        ];
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture', '*.latte');
    }

    private function createContainer(): Container
    {
        $configs = [
            __DIR__ . '/../../../../packages/template-phpstan-compiler/config/services.neon',
            __DIR__ . '/../../../../packages/latte-phpstan-compiler/config/services.neon',
            __DIR__ . '/../../../../packages/phpstan-rules/config/services/services.neon',
            __DIR__ . '/../../../../packages/astral/config/services.neon',
            __DIR__ . '/latte_to_php_compiler_test.neon',
        ];

        $phpStanContainerFactory = new PHPStanContainerFactory();
        return $phpStanContainerFactory->createContainer($configs);
    }
}
