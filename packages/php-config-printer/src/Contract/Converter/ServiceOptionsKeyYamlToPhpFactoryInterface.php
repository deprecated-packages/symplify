<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Contract\Converter;

use PhpParser\Node\Expr\MethodCall;

interface ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yaml,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall;

    public function isMatch(mixed $key, mixed $values): bool;
}
