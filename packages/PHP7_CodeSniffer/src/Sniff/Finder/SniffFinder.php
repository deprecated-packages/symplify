<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Sniff\Finder;

use Symplify\PHP7_CodeSniffer\Composer\VendorDirProvider;

final class SniffFinder
{
    /**
     * @var string[]|array
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

    private function findAllSniffClassesInDirectory(string $directory) : array
    {
        if (isset($this->sniffClassesPerDirectory[$directory])) {
            return $this->sniffClassesPerDirectory[$directory];
        }

        $robotLoader = $this->sniffClassRobotLoaderFactory->createForDirectory($directory);
        $foundSniffClasses = array_keys($robotLoader->getIndexedClasses());
        $sniffClasses = $this->sniffClassFilter->filterOutAbstractAndNonPhpSniffClasses($foundSniffClasses);

        return $this->sniffClassesPerDirectory[$directory] = $sniffClasses;
    }
}
