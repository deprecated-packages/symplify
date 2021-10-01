<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\TwigCompleteCheckRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\PHPStanRules\Symfony\Tests\Rules\TwigCompleteCheckRule\Source\SomeType;

final class FirstForeachMissing extends AbstractController
{
    public function __invoke(): Response
    {
        $templateFilePath = __DIR__ . '/../Source/non_existing_foreach_simple.twig';

        $someVariable = new SomeType();
        $someTypes = [$someVariable];

        return $this->render($templateFilePath, [
            'some_types' => $someTypes
        ]);
    }
}
