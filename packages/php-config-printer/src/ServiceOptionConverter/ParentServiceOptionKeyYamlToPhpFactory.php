<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\BuilderHelpers;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\NodeFactory\CommonNodeFactory;
use Symplify\PhpConfigPrinter\ServiceOptionAnalyzer\ServiceOptionAnalyzer;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ParentServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
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
        $methodCall = new MethodCall($methodCall, $method);
        $methodCall->args[] = new Arg(BuilderHelpers::normalizeValue($values[$key]));

        return $methodCall;
    }

    public function isMatch($key, $values): bool
    {
        return $key === YamlKey::PARENT;
    }
}
