<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipTemplateSetVariable extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/../Source/template/with_set.twig', [
            'use_me' => 'some_value'
        ]);
    }
}
