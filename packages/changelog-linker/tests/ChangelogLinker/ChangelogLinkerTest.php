<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogLinker;

use Iterator;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @covers \Symplify\ChangelogLinker\ChangelogLinker
 * @covers \Symplify\ChangelogLinker\Worker\UserReferencesWorker
 * @covers \Symplify\ChangelogLinker\Worker\LinkifyWorker
 * @covers \Symplify\ChangelogLinker\Worker\LinksToReferencesWorker
 * @covers \Symplify\ChangelogLinker\Worker\DiffLinksToVersionsWorker
 * @covers \Symplify\ChangelogLinker\Worker\BracketsAroundReferencesWorker
 */
final class ChangelogLinkerTest extends AbstractKernelTestCase
{
    /**
     * @var ChangelogLinker
     */
    private $changelogLinker;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config.php']);
        $this->changelogLinker = self::$container->get(ChangelogLinker::class);
    }

    /**
     * @dataProvider dataProvider()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpected = StaticFixtureSplitter::splitFileInfoToInputAndExpected($fixtureFileInfo);

        $processedContent = $this->changelogLinker->processContentWithLinkAppends($inputAndExpected->getInput());
        $this->assertSame(
            $inputAndExpected->getExpected(),
            $processedContent,
            $fixtureFileInfo->getRelativeFilePathFromCwd()
        );
    }

    public function dataProvider(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.md');
    }
}
