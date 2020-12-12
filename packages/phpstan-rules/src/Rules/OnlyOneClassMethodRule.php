<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\OnlyOneClassMethodRule\OnlyOneClassMethodRuleTest
 */
final class OnlyOneClassMethodRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use only one of "%s" methods can be implemented';

    /**
     * @var array<string, string[]>
     */
    private $onlyOneMethodsByType = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, string[]> $onlyOneMethodsByType
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $onlyOneMethodsByType = [])
    {
        $this->onlyOneMethodsByType = $onlyOneMethodsByType;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Allow only one of methods to be implemented on type', [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass implements CheckedInterface
{
    public function run()
    {
    }

    public function hide()
    {
    }
}
CODE_SAMPLE
,
                <<<'CODE_SAMPLE'
class SomeClass implements CheckedInterface
{
    public function run()
    {
    }
}
CODE_SAMPLE
,
                [
                    'onlyOneMethodsByType' => [
                        'CheckedInterface' => ['run', 'hide'],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethodsNode::class];
    }

    /**
     * @param ClassMethodsNode $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return [];
        }

        $classMethodNames = [];
        foreach ($node->getMethods() as $classMethod) {
            $classMethodNames[] = $this->simpleNameResolver->getName($classMethod->name);
        }

        foreach ($this->onlyOneMethodsByType as $type => $methods) {
            if (! is_a($className, $type, true)) {
                continue;
            }

            $usedMethods = array_intersect($classMethodNames, $methods);
            if (count($usedMethods) < 2) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, implode('", "', $usedMethods));
            return [$errorMessage];
        }

        return [];
    }
}
