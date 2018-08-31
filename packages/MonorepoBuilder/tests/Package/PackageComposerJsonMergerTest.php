<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\MonorepoBuilder\Tests\AbstractContainerAwareTestCase;

final class PackageComposerJsonMergerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = $this->container->get(PackageComposerJsonMerger::class);
    }

    public function testMergeRequire(): void
    {
        $files = $this->getFileInfos();
        $merged = $this->packageComposerJsonMerger->mergeFileInfos($files, ['require']);

        $this->assertSame([
            'require' => [
                'rector/rector' => '^2.0',
                'symplify/symplify' => '^2.0',
            ],
        ], $merged);
    }

    public function testMergeAutoload(): void
    {
        $files = $this->getFileInfos();

        $merged = $this->packageComposerJsonMerger->mergeFileInfos($files, ['autoload']);

        $this->assertSame([
            'autoload' => [
                'psr-4' => [
                    'Symplify\Statie\\' => 'src',
                    'Symplify\MonorepoBuilder\\' => 'src',
                ],
            ],
        ], $merged);
    }

    /**
     * @return SplFileInfo[]
     */
    private function getFileInfos(): array
    {
        $iterator = Finder::create()->files()
            ->in(__DIR__ . '/Source')
            ->getIterator();

        return iterator_to_array($iterator);
    }
}
