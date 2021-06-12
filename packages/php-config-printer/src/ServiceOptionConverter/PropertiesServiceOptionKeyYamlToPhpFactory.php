<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ServiceOptionConverter;

use PhpParser\Node\Expr\MethodCall;
use Symplify\PhpConfigPrinter\Contract\Converter\ServiceOptionsKeyYamlToPhpFactoryInterface;
use Symplify\PhpConfigPrinter\NodeFactory\Service\SingleServicePhpNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\YamlServiceKey;

final class PropertiesServiceOptionKeyYamlToPhpFactory implements ServiceOptionsKeyYamlToPhpFactoryInterface
{
    public function __construct(
        private SingleServicePhpNodeFactory $singleServicePhpNodeFactory
    ) {
    }

    public function decorateServiceMethodCall($key, $yaml, $values, MethodCall $methodCall): MethodCall
    {
        return $this->singleServicePhpNodeFactory->createProperties($methodCall, $yaml);
    }

    public function isMatch($key, $values): bool
    {
        return $key === YamlServiceKey::PROPERTIES;
    }
}
