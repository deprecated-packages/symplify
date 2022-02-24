<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Contract\Converter;

use PhpParser\Node\Expr\MethodCall;

interface ServiceOptionsKeyYamlToPhpFactoryInterface
{
    /**
     * @param mixed $key
     * @param mixed|mixed[] $yaml
     * @param mixed $values
     */
    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall;

    public function isMatch(mixed $key, mixed $values): bool;
}
