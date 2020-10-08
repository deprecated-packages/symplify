<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * @see \Symplify\EasyHydrator\Tests\ArrayToValueObjectHydratorTest
 */
final class ArrayToValueObjectHydrator
{
    /**
     * @var FilesystemAdapter
     */
    private $filesystemAdapter;

    /**
     * @var ValueResolver
     */
    private $valueResolver;

    public function __construct(FilesystemAdapter $filesystemAdapter, ValueResolver $valueResolver)
    {
        $this->filesystemAdapter = $filesystemAdapter;
        $this->valueResolver = $valueResolver;
    }

    /**
     * @param mixed[] $data
     */
    public function hydrateArray(array $data, string $class): object
    {
        $arrayHash = md5(serialize($data) . $class);

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->filesystemAdapter->getItem($arrayHash);
        if ($cacheItem->get() !== null) {
            // return $cacheItem->get();
        }

        $arguments = $this->valueResolver->resolveClassConstructorValues($class, $data);

        $value = new $class(...$arguments);

        $cacheItem->set($value);
        $this->filesystemAdapter->save($cacheItem);

        return $value;
    }

    /**
     * @param mixed[][] $datas
     * @return object[]
     */
    public function hydrateArrays(array $datas, string $class): array
    {
        $objects = [];
        foreach ($datas as $data) {
            $objects[] = $this->hydrateArray($data, $class);
        }

        return $objects;
    }
}
