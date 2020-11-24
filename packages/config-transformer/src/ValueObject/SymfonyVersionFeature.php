<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\ValueObject;

final class SymfonyVersionFeature
{
    /**
     * @var float
     * @see https://symfony.com/blog/new-in-symfony-3-4-services-are-private-by-default
     */
    public const PRIVATE_SERVICES_BY_DEFAULT = 3.4;

    /**
     * @var float
     * @see https://github.com/symfony/symfony/pull/20651
     */
    public const TAGS_WITHOUT_NAME = 3.3;

    /**
     * @var float
     * @see https://symfony.com/blog/new-in-symfony-3-3-optional-class-for-named-services
     * @see https://github.com/symfony/symfony/issues/22146#issuecomment-288988780
     */
    public const SERVICE_WITHOUT_NAME = 3.3;

    /**
     * @var float
     * @see https://github.com/symfony/symfony/pull/36800
     */
    public const REF_OVER_SERVICE = 5.1;
}
