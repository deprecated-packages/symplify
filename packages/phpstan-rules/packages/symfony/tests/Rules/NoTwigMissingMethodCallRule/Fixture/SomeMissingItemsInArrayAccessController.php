<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symplify\PHPStanRules\Symfony\Tests\Rules\NoTwigMissingMethodCallRule\Source\SomeArrayAccesType;

final class SomeMissingItemsInArrayAccessController extends AbstractController
{
    public function __invoke(): Response
    {
        $someArrayAccess = new SomeArrayAccesType();

        return $this->render(__DIR__ . '/../Source/non_existing_array_access_items.twig', [
            'some_array_access_type' => $someArrayAccess
        ]);
    }
}
