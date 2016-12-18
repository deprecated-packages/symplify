<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ModularRouting\Contract\Routing;

use Symfony\Component\Routing\RouteCollection;

interface RouteCollectionProviderInterface
{
    public function getRouteCollection() : RouteCollection;
}
