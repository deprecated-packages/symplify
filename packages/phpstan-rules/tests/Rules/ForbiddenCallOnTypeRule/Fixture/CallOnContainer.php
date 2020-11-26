<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenCallOnTypeRule\Fixture;

use Symfony\Component\DependencyInjection\Container;

final class CallOnContainer
{
    /**
     * @var Container
     */
    private $container;

    public function __contruct(Container $container)
    {
        $this->container = $container;
    }

    public function call()
    {
        $this->container->call();
    }
}
