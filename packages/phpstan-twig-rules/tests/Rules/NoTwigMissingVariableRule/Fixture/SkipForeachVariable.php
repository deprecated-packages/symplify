<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\NoTwigMissingVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SkipForeachVariable extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/../Source/template/foreach_variable.twig', [
            'items' => [1, 2, 3]
        ]);
    }
}
