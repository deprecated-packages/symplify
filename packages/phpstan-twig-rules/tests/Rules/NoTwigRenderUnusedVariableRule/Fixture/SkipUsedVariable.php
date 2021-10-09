<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigRenderUnusedVariableRule\Fixture;

use Nette\Application\UI\Control;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SkipUsedVariable extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render(__DIR__ . '/../Source/some_template_using_variable.twig', [
            'use_me' => 'some_value'
        ]);
    }
}
