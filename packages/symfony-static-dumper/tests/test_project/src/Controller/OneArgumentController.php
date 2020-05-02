<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\Tests\TestProject\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symplify\SymfonyStaticDumper\Contract\ControllerWithDataProviderInterface;

final class OneArgumentController extends AbstractController implements ControllerWithDataProviderInterface
{
    /**
     * @Route(path="/one-param/{param}", name="one_param")
     */
    public function __invoke(string $param): Response
    {
        return $this->render('one_param.twig', ['param' => $param]);
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
            '1',
            '2',
        ];
    }
}
