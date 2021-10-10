<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigRenderUnusedVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SkipIncludeArray extends AbstractController
{
    public function __invoke($value): Response
    {
        return $this->render(__DIR__ . '/../Source/use_in_include.twig', [
            'posts' => ['...']
        ]);
    }
}
