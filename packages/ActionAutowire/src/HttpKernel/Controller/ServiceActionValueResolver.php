<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2015 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ActionAutowire\HttpKernel\Controller;

use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symplify\ActionAutowire\DependencyInjection\ServiceLocator;

final class ServiceActionValueResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument) : bool
    {
        return $this->serviceLocator->hasByType($argument->getType());
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument) : Generator
    {
        yield $this->serviceLocator->getByType($argument->getType());
    }
}
