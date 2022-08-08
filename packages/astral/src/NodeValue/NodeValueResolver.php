<?php

declare(strict_types=1);

namespace Symplify\Astral\NodeValue;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Cast;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use Symplify\Astral\Contract\NodeValueResolver\NodeValueResolverInterface;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\Astral\NodeValue\NodeValueResolver\ClassConstFetchValueResolver;
use Symplify\Astral\NodeValue\NodeValueResolver\ConstFetchValueResolver;
use Symplify\Astral\NodeValue\NodeValueResolver\FuncCallValueResolver;
use Symplify\Astral\NodeValue\NodeValueResolver\MagicConstValueResolver;

/**
 * @api
 * @see \Symplify\Astral\Tests\NodeValue\NodeValueResolverTest
 */
final class NodeValueResolver
{
    /**
     * @var array<class-string<Expr>>
     */
    private const UNRESOLVABLE_TYPES = [
        Variable::class, Cast::class, MethodCall::class, PropertyFetch::class, Instanceof_::class,
    ];

    private ConstExprEvaluator $constExprEvaluator;

    private ?string $currentFilePath = null;

    /**
     * @var NodeValueResolverInterface[]
     */
    private array $nodeValueResolvers = [];

    public function __construct()
    {
        $this->constExprEvaluator = new ConstExprEvaluator(fn (Expr $expr) => $this->resolveByNode($expr));

        $this->nodeValueResolvers[] = new ClassConstFetchValueResolver();
        $this->nodeValueResolvers[] = new ConstFetchValueResolver();
        $this->nodeValueResolvers[] = new MagicConstValueResolver();
        $this->nodeValueResolvers[] = new FuncCallValueResolver($this->constExprEvaluator);
    }

    public function resolve(Expr $expr, string $filePath): mixed
    {
        $this->currentFilePath = $filePath;

        try {
            return $this->constExprEvaluator->evaluateDirectly($expr);
        } catch (ConstExprEvaluationException) {
            return null;
        }
    }

    private function resolveByNode(Expr $expr): mixed
    {
        if ($this->currentFilePath === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($this->nodeValueResolvers as $nodeValueResolver) {
            if (is_a($expr, $nodeValueResolver->getType(), true)) {
                return $nodeValueResolver->resolve($expr, $this->currentFilePath);
            }
        }

        // these values cannot be resolved in reliable way
        foreach (self::UNRESOLVABLE_TYPES as $unresolvableType) {
            if (is_a($expr, $unresolvableType, true)) {
                throw new ConstExprEvaluationException(
                    'The node "%s" value is not possible to resolve. Provide different one.'
                );
            }
        }

        return null;
    }
}
