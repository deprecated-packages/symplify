<?php

declare(strict_types=1);

namespace Symplify\NeonConfigDumper\Application;

use Nette\Neon\Neon;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCI\ActiveClass\ClassNameResolver;

final class NeonConfigDumper
{
    public function __construct(
        private ClassNameResolver $classNameResolver,
    ) {
    }

    public function generate(string $directory): string|null
    {
        // 1. find files
        $fileInfos = $this->findFileInfosInDirectory($directory);

        // 2. extract class names
        $classNames = $this->classNameResolver->resolveFromFromFileInfos($fileInfos);
        if ($classNames === []) {
            return null;
        }

        // 3. create neon file
        return $this->createNeonFileContent($classNames);
    }

    /**
     * @return SplFileInfo[]
     */
    private function findFileInfosInDirectory(string $directory): array
    {
        $finder = new Finder();

        $serviceFinder = $finder->files()
            ->in($directory)
            ->exclude(['Rules/', 'ValueObject/', 'Contract/', 'Exception/', 'Kernel/'])
            ->sortByName();

        return iterator_to_array($serviceFinder->getIterator());
    }

    /**
     * @param string[] $classNames
     */
    private function createNeonFileContent(array $classNames): string
    {
        $neon = [
            'services' => $classNames,
        ];

        $neonFileContent = Neon::encode($neon, Neon::BLOCK, '    ');
        return rtrim($neonFileContent) . PHP_EOL;
    }
}
