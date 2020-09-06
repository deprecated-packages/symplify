<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;
use Symplify\EasyHydrator\Exception\MissingConstructorException;

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

        /** @var CacheItem $cachedItem */
        $cachedItem = $this->filesystemAdapter->getItem($arrayHash);
        if ($cachedItem->get() !== null) {
            return $cachedItem->get();
        }

        $arguments = $this->resolveClassConstructorValues($class, $data);

        $value = new $class(...$arguments);

        $cachedItem->set($value);
        $this->filesystemAdapter->save($cachedItem);

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

    /**
     * @return array<int, mixed>
     */
    private function resolveClassConstructorValues(string $class, array $data): array
    {
        $arguments = [];

        $parameterReflections = $this->getConstructorParameterReflections($class);
        foreach ($parameterReflections as $parameterReflection) {
            $arguments[] = $this->valueResolver->resolveValue($data, $parameterReflection);
        }

        return $arguments;
    }

    /**
     * @return ReflectionParameter[]
     */
    private function getConstructorParameterReflections(string $class): array
    {
        $classReflection = new ReflectionClass($class);

        $constructorReflectionMethod = $classReflection->getConstructor();
        if ($constructorReflectionMethod === null) {
            throw new MissingConstructorException(sprintf('Hydrated class "%s" is missing constructor.', $class));
        }

        return $constructorReflectionMethod->getParameters();
    }
}
