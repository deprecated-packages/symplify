<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeType;

final class SomeForeachMissingVariableController extends AbstractController
{
    public function __invoke(): Response
    {
        $someVariable = new SomeType();
        $someTypes = [$someVariable];

        return $this->render(__DIR__ . '/../Source/non_existing_method_foreach.twig', [
            'some_types' => $someTypes
        ]);
    }
}
