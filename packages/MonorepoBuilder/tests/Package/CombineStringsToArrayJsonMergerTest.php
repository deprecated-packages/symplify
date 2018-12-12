<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;
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

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = $this->container->get(PackageComposerJsonMerger::class);
        $this->finderSanitizer = $this->container->get(FinderSanitizer::class);
    }

    public function testSharedNamespaces(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/SourceAutoloadSharedNamespaces')
        );

        $this->assertSame([
            'autoload' => [
                'psr-4' => [
                    'ACME\Another\\' => 'packages/A',
                    'ACME\Model\Core\\' => ['packages/A', 'packages/B'],
                    'ACME\\YetAnother\\' => ['packages/A'],
                    'ACME\\YetYetAnother\\' => 'packages/A',
                ],
            ],
        ], $merged);
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
