<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigRenderUnusedVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class SkipControllerRouter extends AbstractController
{
    public function index(RouterInterface $router): Response
    {
        // Passed "variable" variable is not used in the template
        $url = $router->generate('route', ['variable' => 'value']);
    }
}
