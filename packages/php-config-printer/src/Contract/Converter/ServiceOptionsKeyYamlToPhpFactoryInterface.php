<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Contract\Converter;

use PhpParser\Node\Expr\MethodCall;

interface ServiceOptionsKeyYamlToPhpFactoryInterface
{
    /**
<<<<<<< HEAD
     * @param mixed $key
     * @param mixed $yaml
     * @param mixed $values
=======
     * @param string $key
     * @param mixed[] $yaml
     * @param mixed[] $values
>>>>>>> b8ac1a5d0... add more types
     */
    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $serviceMethodCall): MethodCall;

    public function isMatch($key, $values): bool;
}
