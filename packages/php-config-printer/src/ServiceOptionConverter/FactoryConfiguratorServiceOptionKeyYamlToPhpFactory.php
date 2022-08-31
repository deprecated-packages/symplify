<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ServiceOptionConverter\NodeModifier\SingleFactoryReferenceNodeModifier;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class FactoryConfiguratorServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
        private SingleFactoryReferenceNodeModifier $singleFactoryReferenceNodeModifier
    ) {
    }

    public function decorateServiceMethodCall(
        mixed $key,
        mixed $yaml,
        mixed $values,
        MethodCall $methodCall
    ): MethodCall {
        $args = (is_array($yaml) || strpos($yaml, ':') !== false)
            ? $this->argsNodeFactory->createFromValuesAndWrapInArray($yaml)
            : $this->argsNodeFactory->createFromValues($yaml);

        $this->singleFactoryReferenceNodeModifier->modifyArgs($args);

        return new MethodCall($methodCall, 'factory', $args);
    }

    public function isMatch(mixed $key, mixed $values): bool
    {
        return in_array($key, [YamlKey::FACTORY, YamlKey::CONFIGURATOR], true);
    }
}
