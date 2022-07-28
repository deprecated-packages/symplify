<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\CaseConverter;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\Service\AutoBindNodeFactory;
use Symplify\PhpConfigPrinter\ValueObject\MethodName;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;
use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class ServicesDefaultsCaseConverter implements CaseConverterInterface
{
    public function __construct(
        private AutoBindNodeFactory $autoBindNodeFactory
    ) {
    }

    public function convertToMethodCall(mixed $key, mixed $values): Stmt
    {
        $methodCall = new MethodCall($this->createServicesVariable(), MethodName::DEFAULTS);

        $decoratedMethodCall = $this->autoBindNodeFactory->createAutoBindCalls($values, $methodCall);

        return new Expression($decoratedMethodCall);
    }

    public function match(string $rootKey, mixed $key, mixed $values): bool
    {
        if ($rootKey !== YamlKey::SERVICES) {
            return false;
        }

        return $key === YamlKey::_DEFAULTS;
    }

    private function createServicesVariable(): Variable
    {
        return new Variable(VariableName::SERVICES);
    }
}
