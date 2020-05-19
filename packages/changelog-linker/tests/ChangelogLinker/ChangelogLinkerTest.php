<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Tests\ChangelogLinker;

use Iterator;
use Nette\Utils\Strings;
use Symplify\ChangelogLinker\ChangelogLinker;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\PackageBuilder\Tests\StaticFixtureLoader;
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
        $this->bootKernelWithConfigs(ChangelogLinkerKernel::class, [__DIR__ . '/config/test_config.yaml']);
        $this->changelogLinker = self::$container->get(ChangelogLinker::class);
    }

    /**
     * @dataProvider dataProvider()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        [$oldContent, $expectedContent] = Strings::split($fixtureFileInfo->getContents(), "#-----\n#");

        $processedContent = $this->changelogLinker->processContentWithLinkAppends($oldContent);
        $this->assertSame($expectedContent, $processedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function dataProvider(): Iterator
    {
        return StaticFixtureLoader::loadFromDirectory(__DIR__ . '/Fixture');
    }
}
