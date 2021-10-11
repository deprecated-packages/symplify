<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Symfony\Contracts\Cache\CacheInterface;

/**
 * @api
 * @see \Symplify\EasyHydrator\Tests\ArrayToValueObjectHydratorTest
 */
final class ArrayToValueObjectHydrator
{
    public function __construct(
        private CacheInterface $cache,
        private ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
    }

    /**
     * @template T of object
     * @param mixed[] $data
     * @param class-string<T> $class
     * @return T
     */
    public function hydrateArray(array $data, string $class): object
    {
        $serializedData = serialize($data);
        $arrayHash = md5($serializedData . $class);

        return $this->cache->get($arrayHash, function () use ($class, $data): object {
            $resolveClassConstructorValues = $this->classConstructorValuesResolver->resolve($class, $data);

            return new $class(...$resolveClassConstructorValues);
        });
    }

    /**
     * @template T of object
     * @param mixed[][] $datas
     * @param class-string<T> $class
     * @return array<T>
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
