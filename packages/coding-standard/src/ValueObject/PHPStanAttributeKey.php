<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ValueObject;

final class PHPStanAttributeKey
{
    /**
     * @var string
     */
    public const PARENT = 'parent';

    /**
     * @var string
     */
    public const PREVIOUS = 'previous';

    /**
     * @var string
     */
    public const NEXT = 'next';

    /**
     * @var string
     */
    public const STATEMENT_DEPTH = 'statementDepth';
}
