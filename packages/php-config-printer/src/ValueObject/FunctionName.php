<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

final class FunctionName
{
    /**
     * @var string
     */
    public const SERVICE = 'service';

    /**
     * @var string
     */
    public const REF = 'ref';

    /**
     * @var string
     */
    public const EXPR = 'expr';

    /**
     * @var string
     */
    public const INLINE_SERVICE = 'inline_service';
}
