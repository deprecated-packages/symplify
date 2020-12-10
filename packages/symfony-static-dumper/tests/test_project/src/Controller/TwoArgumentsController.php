<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TwoArgumentsController extends AbstractController
{
    /**
     * @Route(path="/{type}/{param}", name="two_params")
     */
    public function __invoke(string $type, string $param): Response
    {
        return $this->render('two_params.twig', [
            'type' => $type,
            'param' => $param,
        ]);
    }
}
