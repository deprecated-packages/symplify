<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Tests\LatteToPhpCompiler;

use Iterator;
use PHPStan\DependencyInjection\Container;
use PHPUnit\Framework\TestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PHPStanExtensions\DependencyInjection\PHPStanContainerFactory;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\SmartFileSystem\SmartFileInfo;

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
        $phpFileContent = $this->latteToPhpCompiler->compileContent($inputAndExpected->getInput());

        // update test fixture if the content has changed
        StaticFixtureUpdater::updateFixtureContent($inputAndExpected->getInput(), $phpFileContent, $fileInfo);

        $this->assertSame($phpFileContent, $inputAndExpected->getExpected());
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
            __DIR__ . '/config/extra-services.neon',
            __DIR__ . '/../../../../config/services/services.neon',
        ];

        $phpStanContainerFactory = new PHPStanContainerFactory();
        return $phpStanContainerFactory->createContainer($configs);
    }
}
