<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Alice;

use Doctrine\ORM\EntityManagerInterface;
use Nelmio\Alice\Fixtures\Loader;
use Nette\Utils\Finder;
use SplFileInfo;
use Zenify\DoctrineFixtures\Contract\Alice\AliceLoaderInterface;
use Zenify\DoctrineFixtures\Exception\MissingSourceException;

final class AliceLoader implements AliceLoaderInterface
{

    /**
     * @var Loader
     */
    private $aliceLoader;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(Loader $aliceLoader, EntityManagerInterface $entityManager)
    {
        $this->aliceLoader = $aliceLoader;
        $this->entityManager = $entityManager;
    }


    /**
     * @param string|array $sources
     * @return object[]
     */
    public function load($sources) : array
    {
        if (! is_array($sources)) {
            $sources = [$sources];
        }

        $entities = [];
        foreach ($sources as $source) {
            $newEntities = $this->loadEntitiesFromSource($source);
            $entities = array_merge($entities, $newEntities);
        }

        $this->entityManager->flush();

        return $entities;
    }


    /**
     * @param mixed $source
     * @return object[]
     */
    private function loadEntitiesFromSource($source) : array
    {
        if (is_dir($source)) {
            return $this->loadFromDirectory($source);
        } elseif (is_file($source)) {
            return $this->loadFromFile($source);
        }

        throw new MissingSourceException(
            sprintf('Source "%s" was not found.', $source)
        );
    }


    /**
     * @return object[]
     */
    private function loadFromFile(string $path) : array
    {
        $entities = $this->aliceLoader->load($path);
        foreach ($entities as $entity) {
            $this->entityManager->persist($entity);
        }
        return $entities;
    }


    private function loadFromDirectory(string $path) : array
    {
        $files = [];
        foreach (Finder::find('*.neon', '*.yaml', '*.yml')->from($path) as $file) {
            /** @var SplFileInfo $file */
            $files[] = $file->getPathname();
        }
        return $this->load($files);
    }
}
