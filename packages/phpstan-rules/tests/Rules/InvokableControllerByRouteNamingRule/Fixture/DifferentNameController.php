<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\InvokableControllerByRouteNamingRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class DifferentNameController extends AbstractController
{
    #[Route(path: '/logout', name: 'logout')]
    public function __invoke(): Response
    {
    }
}