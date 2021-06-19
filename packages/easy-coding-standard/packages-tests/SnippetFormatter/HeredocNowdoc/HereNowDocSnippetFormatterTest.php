<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SnippetFormatter\HeredocNowdoc;

use Iterator;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetKind;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * For testing approach @see https://github.com/symplify/easy-testing
 */
final class HereNowDocSnippetFormatterTest extends AbstractKernelTestCase
{
    private SnippetFormatter $snippetFormatter;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->snippetFormatter = $this->getService(SnippetFormatter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfos = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $configuration = new Configuration(isFixer: true);

        $changedContent = $this->snippetFormatter->format(
            $inputAndExpectedFileInfos->getInputFileInfo(),
            SnippetPattern::HERENOWDOC_SNIPPET_REGEX,
            SnippetKind::HERE_NOW_DOC,
            $configuration
        );

        $expectedFileContent = $inputAndExpectedFileInfos->getExpectedFileContent();
        $this->assertSame($expectedFileContent, $changedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.php.inc');
    }
}
