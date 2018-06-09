<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;
use Symplify\PackageBuilder\Yaml\ParametersMerger;

final class PackageComposerJsonMergerTest extends TestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = new PackageComposerJsonMerger(new ParametersMerger());
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
