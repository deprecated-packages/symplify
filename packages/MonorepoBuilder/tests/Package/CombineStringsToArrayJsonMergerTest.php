<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
use Symplify\MonorepoBuilder\Tests\ArraySorter;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class CombineStringsToArrayJsonMergerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    /**
     * @var ArraySorter
     */
    private $arraySorter;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = $this->container->get(PackageComposerJsonMerger::class);
        $this->finderSanitizer = $this->container->get(FinderSanitizer::class);
        $this->arraySorter = new $this->container->get(ArraySorter::class);
    }

    public function testSharedNamespaces(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/SourceAutoloadSharedNamespaces')
        );
        $original = [
            'autoload' => [
                'psr-4' => [
                    'ACME\Model\Core\\' => ['packages/A', 'packages/B'],
                    'ACME\Another\\' => 'packages/A',
                    'ACME\\YetAnother\\' => ['packages/A'],
                    'ACME\\YetYetAnother\\' => 'packages/A',
                ],
            ],
        ];

        $original = $this->arraySorter->recursiveSort($original);
        $merged = $this->arraySorter->recursiveSort($merged);

        $this->assertSame($original, $merged);
    }

    /**
     * @return SmartFileInfo[]
     */
    private function getFileInfosFromDirectory(string $directory): array
    {
        $finder = Finder::create()->files()
            ->in($directory)
            ->name('*.json');

        return $this->finderSanitizer->sanitize($finder);
    }
}
