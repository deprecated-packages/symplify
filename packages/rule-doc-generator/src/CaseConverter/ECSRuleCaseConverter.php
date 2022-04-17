<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\CaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;

final class ECSRuleCaseConverter implements CaseConverterInterface
{
    /**
     * @var string
     */
    public const NAME = 'ecsConfig';

    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
    ) {
    }

    public function match(string $rootKey, mixed $key, mixed $values): bool
    {
        return $rootKey === self::NAME;
    }

    public function convertToMethodCall(mixed $key, mixed $values): Expression
    {
        $rectorClass = $values['class'];
        $configuration = $values['configuration'] ?? null;

        $classConstFetch = new ClassConstFetch(new FullyQualified($rectorClass), 'class');
        $args = [new Arg($classConstFetch)];

        $methodName = $configuration ? 'ruleWithConfiguration' : 'rule';

        if ($configuration) {
            $array = $this->argsNodeFactory->resolveExprFromArray($configuration);
            $args[] = new Arg($array);
        }

        $ruleMethodCall = new MethodCall(new Variable(self::NAME), $methodName, $args);

        return new Expression($ruleMethodCall);
    }
}
