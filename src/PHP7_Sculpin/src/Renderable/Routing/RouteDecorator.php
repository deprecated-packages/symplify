<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Renderable\Routing;

use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Contract\Renderable\DecoratorInterface;
use Symplify\PHP7_Sculpin\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\PHP7_Sculpin\Renderable\File\File;

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

    public function decorateFile(File $file)
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
