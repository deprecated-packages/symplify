<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;

final class AutowiringTypesOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private readonly ArgsNodeFactory $argsNodeFactory,
    ) {
    }

    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yaml,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall {
        $args = $this->argsNodeFactory->createFromValues($yaml);
        return new MethodCall($methodCall, 'addAutowiringType', $args);
    }

    public function isMatch(mixed $key, mixed $values): bool
    {
        return $key === 'autowiring_types';
    }
}
