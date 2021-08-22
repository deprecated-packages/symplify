<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingVariableRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

final class SomeMissingVariableController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render(__DIR__ . '/../Source/some_template.twig');
    }
}
