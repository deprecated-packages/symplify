<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Controller\Routing;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait ControllerRoutingTrait
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $route
     * @param array $parameters
     * @param int $referenceType
     */
    protected function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) : string
    {
        return $this->router->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status The status code to use for the Response
     */
    protected function redirect($url, $status = 302) : RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route      The name of the route
     * @param array  $parameters An array of parameters
     * @param int    $status     The status code to use for the Response
     */
    protected function redirectToRoute($route, array $parameters = array(), $status = 302) : RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }
}
