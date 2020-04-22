<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ApiMethodController extends AbstractController
{
    /**
     * @Route(path="api.json")
     */
    public function api(): Response
    {
        return $this->json([
            'key' => 'value',
        ]);
    }
}
