<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeType;

final class SkipExistingProperty extends AbstractController
{
    public function __invoke()
    {
        return $this->render(__DIR__ . '/../Source/skip_existing_property.twig', [
            'some_type' => new SomeType()
        ]);
    }
}
