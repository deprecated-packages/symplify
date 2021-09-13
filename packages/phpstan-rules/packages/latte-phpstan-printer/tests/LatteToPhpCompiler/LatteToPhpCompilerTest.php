<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\Tests\LatteToPhpCompiler;

use Iterator;
use Latte\Parser;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureUpdater;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\LineCommentCorrector;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\LineCommentMatcher;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\Macros\LatteMacroFaker;
use Symplify\PHPStanRules\LattePHPStanPrinter\Latte\UnknownMacroAwareLatteCompiler;
use Symplify\PHPStanRules\LattePHPStanPrinter\LatteToPhpCompiler;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SmartFileSystem\SmartFileSystem;

final class LatteToPhpCompilerTest extends TestCase
{
    private LatteToPhpCompiler $latteToPhpCompiler;

    protected function setUp(): void
    {
        $unknownMacroAwareLatteCompiler = new UnknownMacroAwareLatteCompiler(
            new PrivatesAccessor(),
            new LatteMacroFaker(),
        );

        $simpleNameResolverStaticFactory = SimpleNameResolverStaticFactory::create();

        $latteParser = new Parser();
        $lineCommentCorrector = new LineCommentCorrector(new LineCommentMatcher());

        $this->latteToPhpCompiler = new LatteToPhpCompiler(
            new SmartFileSystem(),
            $latteParser,
            $unknownMacroAwareLatteCompiler,
            $simpleNameResolverStaticFactory,
            new Standard(),
            $lineCommentCorrector
        );
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
}
