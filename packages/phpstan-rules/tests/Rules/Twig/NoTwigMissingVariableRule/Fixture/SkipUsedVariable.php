<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Twig\NoTwigMissingVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipUsedVariable extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/../Source/some_template_using_variable.twig', [
            'use_me' => 'some_value'
        ]);
    }
}
