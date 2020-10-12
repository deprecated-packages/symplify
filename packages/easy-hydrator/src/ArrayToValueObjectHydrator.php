<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;

/**
 * @see \Symplify\EasyHydrator\Tests\ArrayToValueObjectHydratorTest
 */
final class ArrayToValueObjectHydrator
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var ClassConstructorValuesResolver
     */
    private $classConstructorValuesResolver;

    public function __construct(CacheInterface $cache, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $this->cache = $cache;
        $this->classConstructorValuesResolver = $classConstructorValuesResolver;
    }

    /**
     * @param mixed[] $data
     */
    public function hydrateArray(array $data, string $class): object
    {
        $arrayHash = md5(serialize($data) . $class);

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem($arrayHash);
        if ($cacheItem->get() !== null) {
            return $cacheItem->get();
        }

        $resolveClassConstructorValues = $this->classConstructorValuesResolver->resolve($class, $data);

        $valueObject = new $class(...$resolveClassConstructorValues);

        $cacheItem->set($valueObject);
        $this->cache->save($cacheItem);

        return $valueObject;
    }

    /**
     * @param mixed[][] $datas
     * @return object[]
     */
    public function hydrateArrays(array $datas, string $class): array
    {
        $valueObjects = [];
        foreach ($datas as $data) {
            $valueObjects[] = $this->hydrateArray($data, $class);
        }

        return $valueObjects;
    }
}
