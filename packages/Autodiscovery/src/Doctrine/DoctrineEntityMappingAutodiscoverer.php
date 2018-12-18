<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Doctrine;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\Autodiscovery\Contract\AutodiscovererInterface;
use Symplify\Autodiscovery\FileSystem;
use Symplify\Autodiscovery\NamespaceDetector;

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

    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->namespaceDetector = new NamespaceDetector();
        $this->fileSystem = new Filesystem($containerBuilder);
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
                'type' => 'annotation',
                'dir' => $entityDirectory->getRealPath(),
                'prefix' => $namespace,
                'is_bundle' => false, // performance
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
}
