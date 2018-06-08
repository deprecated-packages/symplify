<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Tests\Package;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symplify\MonorepoBuilder\Package\PackageComposerJsonMerger;

final class PackageComposerJsonMergerTest extends TestCase
{
    /**
     * @var PackageComposerJsonMerger
     */
    private $packageComposerJsonMerger;

    protected function setUp(): void
    {
        $this->packageComposerJsonMerger = new PackageComposerJsonMerger();
    }

    public function test(): void
    {
        $iterator = Finder::create()->files()
            ->in(__DIR__ . '/Source')
            ->getIterator();

        $files = iterator_to_array($iterator);

        $merged = $this->packageComposerJsonMerger->mergeFileInfos($files, ['require']);

        $this->assertSame([
            'require' => [
                'symplify/symplify' => '^2.0',
                'rector/rector' => '^2.0',
            ],
        ], $merged);
    }
}
