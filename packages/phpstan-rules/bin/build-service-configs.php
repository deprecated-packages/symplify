<?php

declare(strict_types=1);

// builds service.neon to avoid manual maintenance
// @todo turn into micro command with directory as input and --output filepath

use Nette\Neon\Neon;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCI\ActiveClass\ClassNameResolver;
use Symplify\EasyCI\ActiveClass\NodeDecorator\FullyQualifiedNameNodeDecorator;
use Symplify\SmartFileSystem\SmartFileSystem;

require __DIR__ . '/../../../vendor/autoload.php';

$directory = __DIR__ . '/../src';
$outputFile = __DIR__ . '/../config/services/generated-services.neon';

final class ServicesConfigDumper
{
    private SmartFileSystem $smartFileSystem;

    private ClassNameResolver $classNameResolver;

    public function __construct()
    {
        $this->smartFileSystem = new SmartFileSystem();
        $this->classNameResolver = $this->createClassNameResolver();
    }

    public function run(string $directory, string $outputFile): void
    {
        // 1. find files
        $fileInfos = $this->findFileInfosInDirectory($directory);

        // 2. extract class names
        $classNames = [];
        foreach ($fileInfos as $fileInfo) {
            $classNames[] = $this->classNameResolver->resolveFromFromFileInfo($fileInfo);
        }

        // 3. create neon file
        $serviceFileContent = $this->createNeonFileContent($classNames);

        // 4. dump the file contents to target file
        $this->smartFileSystem->dumpFile($outputFile, $serviceFileContent);
    }

    private function createClassNameResolver(): ClassNameResolver
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);

        return new ClassNameResolver($parser, new FullyQualifiedNameNodeDecorator());
    }

    /**
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    private function findFileInfosInDirectory(string $directory): array
    {
        $finder = new Finder();

        $serviceFinder = $finder->files()
            ->in($directory)
            ->exclude(['Rules/', 'ValueObject/', 'Contract/', 'Exception/'])
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

        return Neon::encode($neon, Neon::BLOCK);
    }
}

$servicesConfigDumper = new ServicesConfigDumper();
$servicesConfigDumper->run($directory, $outputFile);
