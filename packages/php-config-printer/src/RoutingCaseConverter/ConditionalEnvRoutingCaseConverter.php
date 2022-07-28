<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\RoutingCaseConverter;

use Nette\Utils\Strings;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\If_;
use Symfony\Contracts\Service\Attribute\Required;
use Symplify\PhpConfigPrinter\Contract\RoutingCaseConverterInterface;
use Symplify\PhpConfigPrinter\NodeFactory\RoutingConfiguratorReturnClosureFactory;
use Symplify\PhpConfigPrinter\ValueObject\VariableName;

final class ConditionalEnvRoutingCaseConverter implements RoutingCaseConverterInterface
{
    private RoutingConfiguratorReturnClosureFactory $routingConfiguratorReturnClosureFactory;

    #[Required]
    public function autowire(
        RoutingConfiguratorReturnClosureFactory $routingConfiguratorReturnClosureFactory
    ): void {
        $this->routingConfiguratorReturnClosureFactory = $routingConfiguratorReturnClosureFactory;
    }

    /**
     * @param mixed[] $values
     */
    public function match(string $key, mixed $values): bool
    {
        return str_starts_with($key, 'when@');
    }

    /**
     * Mirror to https://github.com/symplify/symplify/pull/4179/files, just for routes
     */
    public function convertToMethodCall(string $key, mixed $values): Stmt
    {
        /** @var string $environment */
        $environment = Strings::after($key, 'when@');

        $variable = new Variable(VariableName::ROUTING_CONFIGURATOR);
        $identical = new Identical(new MethodCall($variable, 'env'), new String_($environment));

        $stmts = $this->routingConfiguratorReturnClosureFactory->createClosureStmts($values);

        return new If_($identical, [
            'stmts' => $stmts,
        ]);
    }
}
