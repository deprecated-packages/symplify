<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\Tests\TwigToPhpCompiler;

use Iterator;
use PHPStan\DependencyInjection\Container;
use PHPStan\Type\StringType;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\LattePHPStanCompiler\ValueObject\VariableAndType;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\TwigPHPStanCompiler\TwigToPhpCompiler;

final class TwigToPhpCompilerTest extends TestCase
{
    private TwigToPhpCompiler $twigToPhpCompiler;

    protected function setUp(): void
    {
        $container = $this->createContainer();
        $this->twigToPhpCompiler = $container->getByType(TwigToPhpCompiler::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $inputFileInfoAndExpected = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpected($fileInfo);
        $phpFileContent = $this->twigToPhpCompiler->compileContent(
            $inputFileInfoAndExpected->getInputFileRealPath(),
            []
        );

        // update test fixture if the content has changed
        StaticFixtureUpdater::updateFixtureContent(
            $inputFileInfoAndExpected->getInputFileContent(),
            $phpFileContent,
            $fileInfo
        );

        $this->assertStringMatchesFormat($inputFileInfoAndExpected->getExpected(), $phpFileContent);
    }

    public function testTypes(): void
    {
        $variablesAndTypes = [new VariableAndType('someName', new StringType())];
        $phpFileContent = $this->twigToPhpCompiler->compileContent(
            __DIR__ . '/FixtureWithTypes/input_file.twig',
            $variablesAndTypes
        );

        $this->assertStringMatchesFormatFile(__DIR__ . '/FixtureWithTypes/expected_compiled.php', $phpFileContent);
    }

    /**
     * @return Iterator<SmartFileInfo>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectoryExclusively(__DIR__ . '/Fixture', '*.twig');
    }

    private function createContainer(): Container
    {
        $configs = [__DIR__ . '/../../../../packages/phpstan-rules/config/services/services.neon'];

        $phpStanContainerFactory = new PHPStanContainerFactory();
        return $phpStanContainerFactory->createContainer($configs);
    }
}
