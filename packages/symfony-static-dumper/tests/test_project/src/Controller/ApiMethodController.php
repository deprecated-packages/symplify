<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class ApiMethodController extends AbstractController
{
    #[Route(path: 'api.json')]
    public function __invoke(): JsonResponse
    {
        return $this->json([
            'key' => 'value',
        ]);
    }
}
