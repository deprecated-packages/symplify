<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\Sorter;

use Iterator;
use Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter;
use Symplify\ComposerJsonManipulator\Tests\HttpKernel\ComposerJsonManipulatorKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ComposerPackageSorterTest extends AbstractKernelTestCase
{
    /**
     * @var ComposerPackageSorter
     */
    private $composerPackageSorter;

    protected function setUp(): void
    {
        $this->bootKernel(ComposerJsonManipulatorKernel::class);

        $this->composerPackageSorter = $this->getService(ComposerPackageSorter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(array $packages, array $expectedSortedPackages): void
    {
        $sortedPackages = $this->composerPackageSorter->sortPackages($packages);
        $this->assertSame($expectedSortedPackages, $sortedPackages);
    }

    public function provideData(): Iterator
    {
        yield [
            [
                'symfony/console' => '^5.2',
                'php' => '^8.0',
                'ext-json' => '*',
            ],
            [
                'php' => '^8.0',
                'ext-json' => '*',
                'symfony/console' => '^5.2',
            ],
        ];
    }
}
