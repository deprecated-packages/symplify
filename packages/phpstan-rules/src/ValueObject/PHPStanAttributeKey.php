<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class PHPStanAttributeKey
{
    /**
     * Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     */
    public const PARENT = 'parent';

    /**
     * Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     */
    public const PREVIOUS = 'previous';

    /**
     * Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     */
    public const NEXT = 'next';

    /**
     * Do not change, part of internal PHPStan naming
     *
     * @api
     * @var string
     */
    public const STATEMENT_DEPTH = 'statementDepth';
}
