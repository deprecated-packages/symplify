<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder;

use Symplify\EasyCI\Kernel\EasyCIKernel;
use Symplify\EasyCI\Testing\UnitTestFilePathsFinder;
use Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\Fixture\OldSchoolTest;
use Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\Fixture\RandomTest;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class UnitTestFilePathsFinderTest extends AbstractKernelTestCase
{
    private UnitTestFilePathsFinder $unitTestFilePathsFinder;

    protected function setup(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->unitTestFilePathsFinder = $this->getService(UnitTestFilePathsFinder::class);
    }

    public function test(): void
    {
        $unitTestFilePaths = $this->unitTestFilePathsFinder->findInDirectories([__DIR__ . '/Fixture']);
        $this->assertCount(2, $unitTestFilePaths);

        $this->assertArrayHasKey(RandomTest::class, $unitTestFilePaths);
        $this->assertArrayHasKey(OldSchoolTest::class, $unitTestFilePaths);
    }
}
