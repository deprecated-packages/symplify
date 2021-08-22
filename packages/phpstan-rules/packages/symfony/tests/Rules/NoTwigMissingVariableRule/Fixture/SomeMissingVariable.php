<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class SomeMissingVariable extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/../Source/some_template.twig');
    }
}
