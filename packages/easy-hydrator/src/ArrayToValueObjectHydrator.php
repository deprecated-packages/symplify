<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

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
        $serializedData = serialize($data);
        $arrayHash = md5($serializedData . $class);

        return $this->cache->get($arrayHash, function () use ($class, $data) {
            $resolveClassConstructorValues = $this->classConstructorValuesResolver->resolve($class, $data);

            return new $class(...$resolveClassConstructorValues);
        });
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
