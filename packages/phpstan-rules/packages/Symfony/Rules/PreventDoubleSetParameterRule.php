<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantStringType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyPhpConfigClosureAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\PreventDoubleSetParameterRule\PreventDoubleSetParameterRuleTest
 *
 * @implements Rule<Closure>
 */
final class PreventDoubleSetParameterRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Set param value is overriden. Merge it to previous set above';

    /**
     * @var array<string, string[]>
     */
    private array $setParametersNamesByFile = [];

    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private SymfonyPhpConfigClosureAnalyzer $symfonyPhpConfigClosureAnalyzer,
        private NodeFinder $nodeFinder,
    ) {
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Closure::class;
    }

    /**
     * @param Closure $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->symfonyPhpConfigClosureAnalyzer->isSymfonyPhpConfig($node)) {
            return [];
        }

        /** @var MethodCall[] $methodCalls */
        $methodCalls = $this->nodeFinder->findInstanceOf($node, MethodCall::class);

        $errorMessages = [];

        foreach ($methodCalls as $methodCall) {
            if (! $this->simpleNameResolver->isName($methodCall->name, 'set')) {
                continue;
            }

            if (! $this->simpleNameResolver->isName($methodCall->var, 'parameters')) {
                continue;
            }

            $firstArg = $methodCall->getArgs()[0];
            $firstArgType = $scope->getType($firstArg->value);

            if (! $firstArgType instanceof ConstantStringType) {
                continue;
            }

            $setParameterName = $firstArgType->getValue();
            $previousSetParameterNames = $this->setParametersNamesByFile[$scope->getFile()] ?? [];

            if (in_array($setParameterName, $previousSetParameterNames, true)) {
                $errorMessages[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->line($methodCall->getLine())
                    ->build();
            }

            $this->setParametersNamesByFile[$scope->getFile()][] = $setParameterName;
        }

        return $errorMessages;
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
}
