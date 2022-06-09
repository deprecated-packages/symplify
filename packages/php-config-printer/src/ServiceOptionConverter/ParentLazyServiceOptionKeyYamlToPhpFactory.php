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
    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yaml,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall {
        $args = [new Arg(BuilderHelpers::normalizeValue($values[$key]))];

        return new MethodCall($methodCall, $key, $args);
    }

    public function isMatch(mixed $key, mixed $values): bool
    {
        return in_array($key, [YamlKey::PARENT, YamlKey::LAZY], true);
    }
}
