<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\SymfonyPhpConfigClosureAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDoubleSetParameterRule\PreventDoubleSetParameterRuleTest
 */
final class PreventDoubleSetParameterRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Set param "%s" value is duplicated, use unique value instead';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var SymfonyPhpConfigClosureAnalyzer
     */
    private $symfonyPhpConfigClosureAnalyzer;

    /**
     * @var array<string, string[]>
     */
    private $setParametersNamesByFile = [];

    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        SymfonyPhpConfigClosureAnalyzer $symfonyPhpConfigClosureAnalyzer,
        ConstExprEvaluator $constExprEvaluator
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->symfonyPhpConfigClosureAnalyzer = $symfonyPhpConfigClosureAnalyzer;
        $this->constExprEvaluator = $constExprEvaluator;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->symfonyPhpConfigClosureAnalyzer->isSymfonyPhpConfigScope($scope)) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->name, 'set')) {
            return [];
        }

        if (! $this->simpleNameResolver->isName($node->var, 'parameters')) {
            return [];
        }

        $setParameterName = $this->constExprEvaluator->evaluateDirectly($node->args[0]->value);
        if (! is_string($setParameterName)) {
            return [];
        }

        $setParameterName = $this->getSetParameterName($node->args[0]->value);
        $previousSetParameterNames = $this->setParametersNamesByFile[$scope->getFile()] ?? [];

        if (in_array($setParameterName, $previousSetParameterNames, true)) {
            $errorMessage = sprintf(self::ERROR_MESSAGE, $setParameterName);
            return [$errorMessage];
        }

        $this->setParametersNamesByFile[$scope->getFile()][] = $setParameterName;

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1]);
    $parameters->set('some_param', [2]);
};
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('some_param', [1, 2]);
};
CODE_SAMPLE
            ),
        ]);
    }

    private function getSetParameterName(Expr $expr): string
    {
        if ($expr instanceof ClassConstFetch && $expr->class instanceof FullyQualified && $expr->name instanceof Identifier) {
            $name = $expr->name;
            return (string) $expr->class . '::' . (string) $name;
        }

        if ($expr instanceof String_) {
            return $expr->value;
        }

        return '';
    }
}
