<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\TwigCompleteCheckRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\PHPStanTwigRules\Tests\Rules\TwigCompleteCheckRule\Source\SomeType;

final class SomeMissingVariableController extends AbstractController
{
    public function __invoke(): Response
    {
        $someVariable = new SomeType();

        return $this->render(__DIR__ . '/../Source/non_existing_method.twig', [
            'some_type' => $someVariable
        ]);
    }
}
