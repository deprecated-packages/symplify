<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeIteratorType;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeType;

final class SomeMissingItemsInIteratorController extends AbstractController
{
    public function __invoke(): Response
    {
        $parent = new SomeIteratorType();
        $someIterator = new SomeIteratorType($parent);

        return $this->render(__DIR__ . '/../Source/non_existing_traversable_items.twig', [
            'some_iterator_type' => $someIterator
        ]);
    }
}
