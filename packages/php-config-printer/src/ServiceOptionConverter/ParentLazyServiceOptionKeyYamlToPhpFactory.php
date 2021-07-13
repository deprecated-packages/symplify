<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ParentLazyServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        $method = $key;
        $methodCall = new MethodCall($methodCall, $method);
        $methodCall->args[] = new Arg(BuilderHelpers::normalizeValue($values[$key]));

        return $methodCall;
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, [YamlKey::PARENT, YamlKey::LAZY], true);
    }
}
