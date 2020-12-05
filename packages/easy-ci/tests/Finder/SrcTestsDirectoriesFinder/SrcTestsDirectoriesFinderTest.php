<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Finder\SrcTestsDirectoriesFinder;

use Symplify\EasyCI\Finder\SrcTestsDirectoriesFinder;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\EasyCI\ValueObject\SrcAndTestsDirectories;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SrcTestsDirectoriesFinderTest extends AbstractKernelTestCase
{
    /**
     * @var SrcTestsDirectoriesFinder
     */
    private $srcTestsDirectoriesFinder;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->srcTestsDirectoriesFinder = $this->getService(SrcTestsDirectoriesFinder::class);
    }

    public function test(): void
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories(
            [__DIR__ . '/Fixture/only_test'],
            true
        );

        $this->assertNotNull($srcAndTestsDirectories);

        /** @var SrcAndTestsDirectories $srcAndTestsDirectories */
        $this->assertCount(0, $srcAndTestsDirectories->getRelativePathSrcDirectories());
        $this->assertCount(1, $srcAndTestsDirectories->getRelativePathTestsDirectories());
    }

    public function testNothing(): void
    {
        $srcAndTestsDirectories = $this->srcTestsDirectoriesFinder->findSrcAndTestsDirectories(
            [__DIR__ . '/Fixture/nothing']
        );
        $this->assertNull($srcAndTestsDirectories);
    }
}
