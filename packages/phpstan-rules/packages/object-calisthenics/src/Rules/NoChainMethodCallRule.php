<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use DateTimeInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PharIo\Version\Version;
use PharIo\Version\VersionNumber;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\PassedByReference;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\RouteCollection;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TwitterAPIExchange;

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
        TwitterAPIExchange::class,
        AbstractConfigurator::class,
        Alias::class,
        Finder::class,
        Definition::class,
        VersionNumber::class,
        Version::class,
        RouteCollection::class,
        TrinaryLogic::class,
        // also trinary logic ↓
        PassedByReference::class,
        DateTimeInterface::class,
        // Doctrine
        QueryBuilder::class,
        Query::class,
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
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$fluentClass = new FluentClass();
$fluentClass->one()->two();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$fluentClass = new FluentClass();
$fluentClass->one()->two();
CODE_SAMPLE
                ,
                [
                    'allowedChainTypes' => ['FluentClass'],
                ]
            ),
        ]);
    }

    private function shouldSkipType(Scope $scope, MethodCall $methodCall): bool
    {
        $methodCallType = $scope->getType($methodCall);
        $callerType = $scope->getType($methodCall->var);

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
