<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;

final class CombineStringsToArrayJsonMergerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = $this->container->get(PackageComposerJsonMerger::class);
    }

    public function testSharedNamespaces(): void
    {
        $merged = $this->packageComposerJsonMerger->mergeFileInfos(
            $this->getFileInfosFromDirectory(__DIR__ . '/SourceAutoloadSharedNamespaces')
        );

        $this->assertSame([
            'autoload' => [
                'psr-4' => [
                    'ACME\Model\Core\\' => ['packages/A', 'packages/B'],
                    'ACME\Another\\' => 'packages/A',
                    'ACME\\YetAnother\\' => ['packages/A'],
                    'ACME\\YetYetAnother\\' => 'packages/A',
                ],
            ],
        ], $merged);
    }

    /**
     * @return SplFileInfo[]
     */
    private function getFileInfosFromDirectory(string $directory): array
    {
        $iterator = Finder::create()->files()
            ->in($directory)
            ->name('*.json')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
