<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\Sniff\Finder;

use Symplify\PHP7_CodeSniffer\Composer\VendorDirProvider;

final class SniffFinder
{
    /**
     * @var string[]
     */
    private $sniffClassesPerDirectory = [];

    /**
     * @var SniffClassRobotLoaderFactory
     */
    private $sniffClassRobotLoaderFactory;

    /**
     * @var SniffClassFilter
     */
    private $sniffClassFilter;

    public function __construct(
        SniffClassRobotLoaderFactory $sniffClassRobotLoaderFactory,
        SniffClassFilter $sniffClassFilter
    ) {
        $this->sniffClassRobotLoaderFactory = $sniffClassRobotLoaderFactory;
        $this->sniffClassFilter = $sniffClassFilter;
    }

    public function findAllSniffClasses() : array
    {
        return $this->findAllSniffClassesInDirectory(VendorDirProvider::provide());
    }

    public function findAllSniffClassesInDirectory(string $directory) : array
    {
        if (isset($this->sniffClassesPerDirectory[$directory])) {
            return $this->sniffClassesPerDirectory[$directory];
        }

        $robotLoader = $this->sniffClassRobotLoaderFactory->createForDirectory($directory);
        $sniffClasses = $this->sniffClassFilter->filterOutAbstractAndNonPhpSniffClasses(
            array_keys($robotLoader->getIndexedClasses())
        );

        return $this->sniffClassesPerDirectory[$directory] = $sniffClasses;
    }
}
