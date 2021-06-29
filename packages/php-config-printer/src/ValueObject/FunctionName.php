<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

final class FunctionName
{
    /**
     * @var string
     */
    public const INLINE_SERVICE = 'Symfony\Component\DependencyInjection\Loader\Configurator\inline_service';

    /**
     * @var string
     */
    public const SERVICE = 'Symfony\Component\DependencyInjection\Loader\Configurator\service';

    /**
     * @var string
     */
    public const REF = 'Symfony\Component\DependencyInjection\Loader\Configurator\ref';

    /**
     * @var string
     */
    public const EXPR = 'Symfony\Component\DependencyInjection\Loader\Configurator\expr';

    /**
     * @var string
     */
    public const TAGGED_ITERATOR = 'Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator';

    /**
     * @var string
     */
    public const TAGGED_LOCATOR = 'Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator';
}
