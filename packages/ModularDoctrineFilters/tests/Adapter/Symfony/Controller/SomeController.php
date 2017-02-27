<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Tests\Adapter\Symfony\Controller;

use Symfony\Component\HttpFoundation\Response;

final class SomeController
{
    public function someAction(): Response
    {
        return new Response;
    }
}
