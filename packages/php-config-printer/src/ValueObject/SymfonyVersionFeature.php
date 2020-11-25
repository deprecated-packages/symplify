<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

final class SymfonyVersionFeature
{
    /**
     * @var float
     * @see https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default
     */
    public const PRIVATE_SERVICES_BY_DEFAULT = 3.4;

    /**
     * @var float
     * @see https://github.com/symfony/symfony/pull/36800
     */
    public const REF_OVER_SERVICE = 5.1;
}
