<?php

declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\AliasingBundle\HttpKernel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

final class SomeControllerResolver implements ControllerResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments(Request $request, $controller)
    {
    }
}
