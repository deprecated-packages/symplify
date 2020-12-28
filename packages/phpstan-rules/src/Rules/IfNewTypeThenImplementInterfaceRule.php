<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\IfNewTypeThenImplementInterfaceRule\IfNewTypeThenImplementInterfaceRuleTest
 */
final class IfNewTypeThenImplementInterfaceRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class must implement "%s" interface';

    /**
     * @var array<string, string>
     */
    private $interfacesByNewTypes = [];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, string> $interfacesByNewTypes
     */
    public function __construct(
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver,
        array $interfacesByNewTypes
    ) {
        $this->interfacesByNewTypes = $interfacesByNewTypes;
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $expectedInterface = $this->resolveExpectedInterface($node);
        if ($expectedInterface === null) {
            return [];
        }

        $className = $this->simpleNameResolver->getName($node);
        if ($className === null) {
            return [];
        }

        if (is_a($className, $expectedInterface, true)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $expectedInterface);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeRule
{
    public function run()
    {
        return new ConfiguredCodeSample('...');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeRule implements ConfiguredRuleInterface
{
    public function run()
    {
        return new ConfiguredCodeSample('...');
    }
}
CODE_SAMPLE
                ,
                [
                    'interfacesByNewTypes' => [
                        'ConfiguredCodeSample' => 'ConfiguredRuleInterface',
                    ],
                ]
            ),
        ]);
    }

    private function resolveExpectedInterface(Class_ $class): ?string
    {
        $expectedInterface = null;

        $this->nodeFinder->findFirst($class, function (Node $node) use (&$expectedInterface) {
            if (! $node instanceof New_) {
                return false;
            }

            foreach ($this->interfacesByNewTypes as $newType => $interface) {
                if (! $this->simpleNameResolver->isName($node->class, $newType)) {
                    continue;
                }

                $expectedInterface = $interface;
                return true;
            }

            return false;
        });

        return $expectedInterface;
    }
}
