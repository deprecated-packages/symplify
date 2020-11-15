<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#5-use-only-one-object-operator---per-statement
 *
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\NoChainMethodCallRuleTest
 */
final class NoChainMethodCallRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use chained method calls. Put each on separated lines.';

    /**
     * @var string[]
     */
    private const DEFAULT_ALLOWED_CHAIN_TYPES = [
        'Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator',
        'Symfony\Component\DependencyInjection\Alias',
        'Symfony\Component\Finder\Finder',
        'Symfony\Component\DependencyInjection\Definition',
        'PharIo\Version\VersionNumber',
        'PharIo\Version\Version',
        'Symfony\Component\Routing\RouteCollection',
        'PHPStan\TrinaryLogic',
    ];

    /**
     * @var string[]
     */
    private $allowedChainTypes = [];

    /**
     * @param string[] $allowedChainTypes
     */
    public function __construct(array $allowedChainTypes = [])
    {
        $this->allowedChainTypes = array_merge(self::DEFAULT_ALLOWED_CHAIN_TYPES, $allowedChainTypes);
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
        if (! $node->var instanceof MethodCall) {
            return [];
        }

        if ($this->shouldSkipType($scope, $node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$this->runThis()->runThat();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->runThis();
$this->runThat();
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipType(Scope $scope, MethodCall $node): bool
    {
        $methodCallType = $scope->getType($node);
        $callerType = $scope->getType($node->var);

        foreach ($this->allowedChainTypes as $allowedChainType) {
            if ($this->isSkippedType($methodCallType, $allowedChainType)) {
                return true;
            }

            if ($this->isSkippedType($callerType, $allowedChainType)) {
                return true;
            }
        }

        return false;
    }

    private function isSkippedType(Type $callerType, string $allowedChainType): bool
    {
        return $callerType instanceof TypeWithClassName && is_a($callerType->getClassName(), $allowedChainType, true);
    }
}
