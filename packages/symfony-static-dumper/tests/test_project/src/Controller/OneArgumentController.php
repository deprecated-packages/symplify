<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class OneArgumentController extends AbstractController
{
    #[Route(path: '/one-param/{param}', name: 'one_param')]
    public function __invoke(string $param): Response
    {
        return $this->render('one_param.twig', [
            'param' => $param,
        ]);
    }
}
