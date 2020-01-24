<?php

declare(strict_types=1);

namespace Symplify\SmartFileSystem\Tests\Finder\FinderSanitizer;

use Nette\Utils\Finder as NetteFinder;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FinderSanitizerTest extends TestCase
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    protected function setUp(): void
    {
        $this->finderSanitizer = new FinderSanitizer();
    }

    public function testValidTypes(): void
    {
        $files = [new SplFileInfo(__DIR__ . '/Source/MissingFile.php')];
        $this->assertCount(0, $this->finderSanitizer->sanitize($files));
    }

    public function testSymfonyFinder(): void
    {
        $finder = SymfonyFinder::create()
            ->files()
            ->in(__DIR__ . '/Source');

        $this->assertCount(2, iterator_to_array($finder->getIterator()));
        $files = $this->finderSanitizer->sanitize($finder);
        $this->assertCount(1, $files);

        $this->validateFile($files[0]);
    }

    public function testNetteFinder(): void
    {
        $finder = NetteFinder::findFiles('*')
            ->from(__DIR__ . '/Source');

        $this->assertCount(2, iterator_to_array($finder->getIterator()));
        $files = $this->finderSanitizer->sanitize($finder);
        $this->assertCount(1, $files);

        $this->validateFile(array_pop($files));
    }

    private function validateFile(SmartFileInfo $smartFileInfo): void
    {
        $this->assertInstanceOf(SymfonySplFileInfo::class, $smartFileInfo);

        $this->assertStringEndsWith('NestedDirectory', $smartFileInfo->getRelativeDirectoryPath());
        $this->assertStringEndsWith('NestedDirectory/FileWithClass.php', $smartFileInfo->getRelativeFilePath());
    }
}
