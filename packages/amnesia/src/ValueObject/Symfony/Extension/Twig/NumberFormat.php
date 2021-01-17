<?php

declare(strict_types=1);

namespace Symplify\Amnesia\ValueObject\Symfony\Extension\Twig;

/**
 * @api
 * @see https://symfony.com/doc/current/reference/configuration/twig.html#number-format
 */
final class NumberFormat
{
    /**
     * @var string
     */
    public const DECIMALS = 'decimals';

    /**
     * @var string
     */
    public const DECIMAL_POINT = 'decimal_point';

    /**
     * @var string
     */
    public const THOUSANDS_SEPARATOR = 'thousands_separator';
}
