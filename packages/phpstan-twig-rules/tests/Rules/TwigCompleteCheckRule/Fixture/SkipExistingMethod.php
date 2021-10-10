<?php

declare(strict_types=1);

namespace Symplify\PHPStanTwigRules\Tests\Rules\TwigCompleteCheckRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symplify\PHPStanTwigRules\Tests\Rules\TwigCompleteCheckRule\Source\SomeType;

final class SkipExistingMethod extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/../Source/skip_existing_method.twig', [
            'some_type' => new SomeType()
        ]);
    }
}
