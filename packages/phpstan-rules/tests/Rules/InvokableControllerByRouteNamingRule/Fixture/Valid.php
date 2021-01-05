<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\InvokableControllerByRouteNamingRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class Valid extends AbstractController
{
    #[Route(path: '/valid', name: 'valid')]
    public function __invoke(): Response
    {
    }
}