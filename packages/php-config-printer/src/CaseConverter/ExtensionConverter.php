<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\MethodName;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ExtensionConverter implements CaseConverterInterface
{
    private ?string $rootKey = null;

    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
        private YamlKey $yamlKey
    ) {
    }

    public function convertToMethodCall($key, $values): Expression
    {
        $args = $this->argsNodeFactory->createFromValues([
            $this->rootKey,
            [
                $key => $values,
            ],
        ]);

        $containerConfiguratorVariable = new Variable(VariableName::CONTAINER_CONFIGURATOR);
        $methodCall = new MethodCall($containerConfiguratorVariable, MethodName::EXTENSION, $args);

        return new Expression($methodCall);
    }

    public function match(string $rootKey, $key, $values): bool
    {
        $this->rootKey = $rootKey;

        return ! in_array($rootKey, $this->yamlKey->provideRootKeys(), true);
    }
}
