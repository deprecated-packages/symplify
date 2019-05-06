<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Doctrine;

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\FileSystem;
use Symplify\Autodiscovery\NamespaceDetector;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class DoctrineEntityMappingAutodiscoverer implements AutodiscovererInterface
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var NamespaceDetector
     */
    private $namespaceDetector;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function __construct(ContainerBuilder $containerBuilder, FileSystem $fileSystem)
    {
        $this->containerBuilder = $containerBuilder;
        $this->namespaceDetector = new NamespaceDetector();
        $this->fileSystem = $fileSystem;
    }

    /**
     * Needs to run before @see \Symfony\Bridge\Doctrine\DependencyInjection\CompilerPass\RegisterMappingsPass
     */
    public function autodiscover(): void
    {
        $entityMappings = [];
        foreach ($this->fileSystem->getEntityDirectories() as $entityDirectory) {
            $namespace = $this->namespaceDetector->detectFromDirectory($entityDirectory);
            if (! $namespace) {
                continue;
            }

            $entityMappings[] = [
                'name' => $namespace, // required name
                'prefix' => $namespace,
                'type' => 'annotation',
                'dir' => $entityDirectory->getRealPath(),
                'is_bundle' => false, // performance
            ];
        }

        $xmlNamespaces = [];

        $directoryByNamespace = $this->resolveDirectoryByNamespace($this->fileSystem->getEntityXmlFiles());
        foreach ($directoryByNamespace as $namespace => $directory) {
            if (in_array($namespace, $xmlNamespaces, true)) {
                continue;
            }

            $xmlNamespaces[] = $namespace;

            $entityMappings[] = [
                'name' => $namespace, // required name
                'prefix' => $namespace,
                'type' => 'xml',
                'dir' => $directory,
                'is_bundle' => false,
            ];
        }

        if (! count($entityMappings)) {
            return;
        }

        // @see https://symfony.com/doc/current/reference/configuration/doctrine.html#mapping-entities-outside-of-a-bundle
        $this->containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => $entityMappings,
            ],
        ]);
    }

    /**
     * @param SmartFileInfo[] $entityXmlFiles
     * @return string[]
     */
    private function resolveDirectoryByNamespace(array $entityXmlFiles): array
    {
        $filesByDirectory = $this->groupFileInfosByDirectory($entityXmlFiles);

        $directoryByNamespace = [];
        foreach ($filesByDirectory as $directory => $filesInDirectory) {
            $commonNamespace = $this->resolveCommonNamespaceForXmlFileInfos($filesInDirectory);
            /** @var string $directory */
            $directoryByNamespace[$commonNamespace] = $directory;
        }

        return $directoryByNamespace;
    }

    /**
     * @param SmartFileInfo[] $smartFileInfos
     * @return SmartFileInfo[][]
     */
    private function groupFileInfosByDirectory(array $smartFileInfos): array
    {
        $filesByDirectory = [];

        foreach ($smartFileInfos as $entityXmlFile) {
            $filesByDirectory[$entityXmlFile->getPath()][] = $entityXmlFile;
        }

        return $filesByDirectory;
    }

    /**
     * @param SmartFileInfo[] $xmlFileInfos
     */
    private function resolveCommonNamespaceForXmlFileInfos(array $xmlFileInfos): string
    {
        $namespaces = [];
        foreach ($xmlFileInfos as $xmlFileInfo) {
            $namespaces[] = $this->namespaceDetector->detectFromXmlFileInfo($xmlFileInfo);
        }

        $commonNamespace = Strings::findPrefix($namespaces);

        return rtrim($commonNamespace, '\\');
    }
}
