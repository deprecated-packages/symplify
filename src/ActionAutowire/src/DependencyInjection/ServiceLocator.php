<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ActionAutowire\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

final class ServiceLocator
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var string[]
     */
    private $serviceByTypeMap;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setServiceByTypeMap(array $serviceByTypeMap)
    {
        $this->serviceByTypeMap = $serviceByTypeMap;
    }

    /**
     * @return object|false
     */
    public function getByType(string $type)
    {
        if (!isset($this->serviceByTypeMap[$type])) {
            return false;
        }

        $serviceIds = $this->serviceByTypeMap[$type];
        $serviceId = reset($serviceIds);

        return $this->container->get($serviceId);
    }

    public function hasByType(string $type) : bool
    {
        return isset($this->serviceByTypeMap[$type]);
    }
}
