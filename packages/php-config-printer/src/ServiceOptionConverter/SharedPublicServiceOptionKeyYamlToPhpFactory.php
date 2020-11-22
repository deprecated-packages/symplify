<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\Exception\NotImplementedYetException;

final class SharedPublicServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        if ($key === 'public') {
            if ($yaml === false) {
                return new MethodCall($methodCall, 'private');
            }

            return new MethodCall($methodCall, 'public');
        }

        throw new NotImplementedYetException();
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, ['shared', 'public'], true);
    }
}
