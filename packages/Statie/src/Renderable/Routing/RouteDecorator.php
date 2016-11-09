<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\Statie\Renderable\Routing;

use Symplify\Statie\Contract\Renderable\DecoratorInterface;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Renderable\File\AbstractFile;

final class RouteDecorator implements DecoratorInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routes = [];

    public function addRoute(RouteInterface $route)
    {
        $this->routes[] = $route;
    }

    public function decorateFile(AbstractFile $file)
    {
        foreach ($this->routes as $route) {
            if ($route->matches($file)) {
                $file->setOutputPath($route->buildOutputPath($file));
                $file->setRelativeUrl($route->buildRelativeUrl($file));

                return;
            }
        }

        $file->setOutputPath($file->getBaseName() . DIRECTORY_SEPARATOR . 'index.html');
        $file->setRelativeUrl($file->getBaseName());
    }
}
