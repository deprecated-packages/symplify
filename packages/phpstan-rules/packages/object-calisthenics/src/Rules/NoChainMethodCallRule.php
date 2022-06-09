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
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\TrinaryLogic;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Loader\Configurator\RouteConfigurator;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\String\AbstractString;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\Matcher\ObjectTypeMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use TwitterAPIExchange;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#5-use-only-one-object-operator---per-statement
 *
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoChainMethodCallRule\NoChainMethodCallRuleTest
 * @implements Rule<MethodCall>
 */
final class NoChainMethodCallRule implements Rule, DocumentedRuleinterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use chained method calls. Put each on separated lines.';

    /**
     * @var array<class-string|string>
     */
    private const DEFAULT_ALLOWED_CHAIN_TYPES = [
        TwitterAPIExchange::class,
        AbstractConfigurator::class,
        RouteConfigurator::class,
        Alias::class,
        Finder::class,
        // symfony
        AbstractString::class,
        // php-scoper finder
        'Isolated\Symfony\Component\Finder\Finder',
        Definition::class,
        VersionNumber::class,
        Version::class,
        RouteCollection::class,
        'Stringy\Stringy',
        // also trinary logic ↓
        PassedByReference::class,
        DateTimeInterface::class,
        // Doctrine
        QueryBuilder::class,
        Query::class,
        'Stringy\Stringy',
        // phpstan
        RuleErrorBuilder::class,
        TrinaryLogic::class,
    ];

    /**
     * @var class-string[]
     */
    private array $allowedChainTypes = [];

    /**
     * @param class-string[] $allowedChainTypes
     */
    public function __construct(
        private ObjectTypeMatcher $objectTypeMatcher,
        array $allowedChainTypes = []
    ) {
        $this->allowedChainTypes = array_merge(self::DEFAULT_ALLOWED_CHAIN_TYPES, $allowedChainTypes);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof MethodCall) {
            return [];
        }

        // skip nullsafe chain
        $isNullsafeChecked = (bool) $node->var->getAttribute(AttributeKey::NULLSAFE_CHECKED);
        if ($isNullsafeChecked === true) {
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
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
$this->runThis()->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()->two();
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->runThis();
$this->runThat();

$fluentClass = new AllowedFluent();
$fluentClass->one()->two();
CODE_SAMPLE
                ,
                [
                    'allowedChainTypes' => ['AllowedFluent'],
                ]
            ),
        ]);
    }

    private function shouldSkipType(Scope $scope, MethodCall $methodCall): bool
    {
        if ($this->objectTypeMatcher->isExprTypes($methodCall, $scope, $this->allowedChainTypes)) {
            return true;
        }

        return $this->objectTypeMatcher->isExprTypes($methodCall->var, $scope, $this->allowedChainTypes);
    }
}
