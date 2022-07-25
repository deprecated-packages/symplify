<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class AbstractServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
    ) {
    }

    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yaml,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall {
        $args = is_bool($yaml) ? $this->argsNodeFactory->createFromValues([$yaml]) : [];

        return new MethodCall($methodCall, new Identifier('abstract'), $args);
    }

    public function isMatch(mixed $key, mixed $values): bool
    {
        return $key === YamlServiceKey::ABSTRACT;
    }
}
