<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ActionAutowire\DependencyInjection\Container;

use ReflectionClass;

/**
 * Inspired by ClassMultiMap
 * https://github.com/skrz/autowiring-bundle/blob/master/DependencyInjection/ClassMultiMap.php.
 */
final class ServicesByTypeMap
{
    /**
     * @var string[]
     */
    private $classes = [];

    public function addService(string $serviceClass, string $serviceId)
    {
        $reflectionClass = new ReflectionClass($serviceClass);
        foreach ($reflectionClass->getInterfaceNames() as $interfaceName) {
            if (! isset($this->classes[$interfaceName])) {
                $this->classes[$interfaceName] = [];
            }
            $this->classes[$interfaceName][] = $serviceId;
        }
        do {
            if (! isset($this->classes[$reflectionClass->getName()])) {
                $this->classes[$reflectionClass->getName()] = [];
            }
            $this->classes[$reflectionClass->getName()][] = $serviceId;
        } while ($reflectionClass = $reflectionClass->getParentClass());
    }

    public function getMap() : array
    {
        return $this->classes;
    }
}
