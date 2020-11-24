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
}
