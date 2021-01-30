<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use ReflectionClass;
use ReflectionClassConstant;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferConstantValueRule\PreferConstantValueRuleTest
 */
final class PreferConstantValueRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use defined constant %s::%s over string %s';

    /**
     * @var array<string, array<string, array<int, string>>>
     */
    private $constantHoldingObjects = [];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @param array<string, array<string, array<int, string>>> $constantHoldingObjects
     */
    public function __construct(SimpleNameResolver $simpleNameResolver, array $constantHoldingObjects = [])
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->constantHoldingObjects = $constantHoldingObjects;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [String_::class];
    }

    /**
     * @param String_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $value = $node->value;
        foreach ($this->constantHoldingObjects as $class => $constants) {
            if (! class_exists($class)) {
                continue;
            }

            $reflectionClass = new ReflectionClass($class);
            foreach ($constants as $constant) {
                $reflectionConstant = $reflectionClass->getReflectionConstant($constant);

                if (! $reflectionConstant instanceof ReflectionClassConstant) {
                    continue;
                }

                if (! $reflectionConstant->isPublic()) {
                    continue;
                }

                if ($value === $reflectionConstant->getValue()) {
                    return [sprintf(self::ERROR_MESSAGE, $class, $constant, $value)];
                }
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return 'require';
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return \ComposerJsonSectoin::REQUIRE;
    }
}
CODE_SAMPLE
                ,
                [
                    'constantHoldingObjects' => [
                        'ComposerJsonSection' => ['REQUIRE'],
                    ],
                ]
            ),
        ]);
    }
}
