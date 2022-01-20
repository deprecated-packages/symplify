<?php

declare(strict_types=1);

namespace Symplify\EasyCI\Testing;

use Symplify\EasyCI\Testing\Autoloading\DualTestCaseAuloader;
use Symplify\EasyCI\Testing\Finder\TestCaseClassFinder;

/**
 * @see \Symplify\EasyCI\Tests\Testing\UnitTestFilePathsFinder\UnitTestFilePathsFinderTest
 */
final class UnitTestFilePathsFinder
{
    public function __construct(
        private DualTestCaseAuloader $dualTestCaseAuloader,
        private TestCaseClassFinder $testCaseClassFinder,
        private UnitTestFilter $unitTestFilter,
    ) {
    }

    /**
     * @param string[] $directories
     * @return array<string, string>
     */
    public function findInDirectories(array $directories): array
    {
        $this->dualTestCaseAuloader->autoload();

        $testsCasesClassesToFilePaths = $this->testCaseClassFinder->findInDirectories($directories);

        return $this->unitTestFilter->filter($testsCasesClassesToFilePaths);
    }
}
