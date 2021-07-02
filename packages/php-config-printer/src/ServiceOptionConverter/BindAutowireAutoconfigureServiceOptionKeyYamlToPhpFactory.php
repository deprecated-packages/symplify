<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\ServiceOptionAnalyzer\ServiceOptionAnalyzer;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class BindAutowireAutoconfigureServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private CommonNodeFactory $commonNodeFactory,
        private ArgsNodeFactory $argsNodeFactory,
        private ServiceOptionAnalyzer $serviceOptionAnalyzer
    ) {
    }

    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        $method = $key;
        if ($key === 'shared') {
            $method = 'share';
        }

        if ($yaml === false) {
            $methodCall = new MethodCall($methodCall, $method);
            $methodCall->args[] = new Arg($this->commonNodeFactory->createFalse());

            return $methodCall;
        }

        if ($yaml === true) {
            $methodCall = new MethodCall($methodCall, $method);
            $methodCall->args[] = new Arg($this->commonNodeFactory->createTrue());

            return $methodCall;
        }

        if (! $this->serviceOptionAnalyzer->hasNamedArguments($yaml)) {
            $args = $this->argsNodeFactory->createFromValuesAndWrapInArray($yaml);
            return new MethodCall($methodCall, 'bind', $args);
        }

        foreach ($yaml as $key => $value) {
            $args = $this->argsNodeFactory->createFromValues([$key, $value], false, true);

            $methodCall = new MethodCall($methodCall, 'bind', $args);
        }

        return $methodCall;
    }

    public function isMatch($key, $values): bool
    {
        return in_array($key, [YamlServiceKey::BIND, YamlKey::AUTOWIRE, YamlKey::AUTOCONFIGURE], true);
    }
}
