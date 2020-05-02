<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;

final class TwoArgumentsController extends AbstractController implements ControllerWithDataProviderInterface
{
    /**
     * @Route(path="/{type}/{param}", name="two_params")
     */
    public function __invoke(string $type, string $param): Response
    {
        return $this->render('two_params.twig', ['type' => $type, 'param' => $param]);
    }

    public function getControllerClass(): string
    {
        return __CLASS__;
    }

    public function getControllerMethod(): string
    {
        return '__invoke';
    }

    public function getArguments(): array
    {
        return [
            ['test', 1],
            ['test', 2],
            ['foo', 1],
            ['foo', 2],
        ];
    }
}
