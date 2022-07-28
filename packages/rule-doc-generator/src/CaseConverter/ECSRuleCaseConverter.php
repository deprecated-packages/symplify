<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\CaseConverter;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Expression;
use Symplify\PhpConfigPrinter\Contract\CaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\ArgsNodeFactory;
use Symplify\PhpConfigPrinter\Printer\ArrayDecorator\ServiceConfigurationDecorator;

final class ECSRuleCaseConverter implements CaseConverterInterface
{
    /**
     * @var string
     */
    public const NAME = 'ecsConfig';

    public function __construct(
        private ArgsNodeFactory $argsNodeFactory,
        private ServiceConfigurationDecorator $serviceConfigurationDecorator,
    ) {
    }

    public function match(string $rootKey, mixed $key, mixed $values): bool
    {
        return $rootKey === self::NAME;
    }

    public function convertToMethodCall(mixed $key, mixed $values): Stmt
    {
        $rectorClass = $values['class'];
        $configuration = $values['configuration'] ?? null;

        $classConstFetch = new ClassConstFetch(new FullyQualified($rectorClass), 'class');
        $args = [new Arg($classConstFetch)];

        $methodName = $configuration ? 'ruleWithConfiguration' : 'rule';

        if ($configuration) {
            $configuration = $this->serviceConfigurationDecorator->decorate($configuration, $rectorClass);
            $array = $this->argsNodeFactory->resolveExprFromArray($configuration);
            $args[] = new Arg($array);
        }

        $ruleMethodCall = new MethodCall(new Variable(self::NAME), $methodName, $args);

        return new Expression($ruleMethodCall);
    }
}
