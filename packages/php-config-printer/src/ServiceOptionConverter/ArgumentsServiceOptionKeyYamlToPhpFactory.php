<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ServiceOptionAnalyzer\ServiceOptionAnalyzer;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class ArgumentsServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
        private ServiceOptionAnalyzer $serviceOptionAnalyzer
    ) {
    }

    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        if (! $this->serviceOptionAnalyzer->hasNamedArguments($yaml)) {
            $args = $this->argsNodeFactory->createFromValuesAndWrapInArray($yaml);
            return new MethodCall($methodCall, 'args', $args);
        }

        foreach ($yaml as $key => $value) {
            $args = $this->argsNodeFactory->createFromValues([$key, $value], false, true);

            $methodCall = new MethodCall($methodCall, 'arg', $args);
        }

        return $methodCall;
    }

    public function isMatch($key, $values): bool
    {
        return $key === YamlServiceKey::ARGUMENTS;
    }
}
