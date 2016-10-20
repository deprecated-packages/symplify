<?php

declare(strict_types=1);

/*
 * This file is part of Symplify.
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\NetteAdapterForSymfonyBundles;

use Nette\DI\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symplify\NetteAdapterForSymfonyBundles\Exception\UnsupportedApiException;

final class SymfonyContainerAdapter implements ContainerInterface
{
    /**
     * @var string[]
     */
    private $symfonyToNetteServiceAliases;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param string[] $symfonyToNetteServiceAliases
     * @param Container $container
     */
    public function __construct(array $symfonyToNetteServiceAliases, Container $container)
    {
        $this->symfonyToNetteServiceAliases = $symfonyToNetteServiceAliases;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $service)
    {
        throw new UnsupportedApiException();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (isset($this->symfonyToNetteServiceAliases[$id])) {
            $id = $this->symfonyToNetteServiceAliases[$id];
        }

        if ($this->has($id)) {
            return $this->container->getService($id);
        }

        throw new ServiceNotFoundException(
            sprintf('Service "%s" was not found.', $id)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->container->hasService($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($name)
    {
        if ($this->hasParameter($name)) {
            return $this->container->getParameters()[$name];
        }

        throw new InvalidArgumentException(
            sprintf('Parameter "%s" was not found.', $name)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($name)
    {
        return isset($this->container->getParameters()[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        throw new UnsupportedApiException();
    }

    /**
     * {@inheritdoc}
     */
    public function initialized($id)
    {
        throw new UnsupportedApiException();
    }
}
