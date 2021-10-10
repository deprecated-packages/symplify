<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigRenderUnusedVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class RenderWithUnusedVariable extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render(__DIR__ . '/../Source/some_template.twig', [
            'unused_variable' => 'some_value'
        ]);
    }
}
