<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeIteratorType;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeType;

final class SkipExistingIteratorItems extends AbstractController
{
    public function __invoke()
    {
        $parent = new SomeIteratorType();
        $someIterator = new SomeIteratorType($parent);

        return $this->render(__DIR__ . '/../Source/skip_existing_traversable_items.twig', [
            'some_iterator_type' => $someIterator
        ]);
    }
}
