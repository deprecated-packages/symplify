<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use ReflectionClass;
use ReflectionClassConstant;
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
     * @var string[]
     */
    private $constantHoldingObjects = [];

    /**
     * @var array<string, ReflectionClassConstant[]>
     */
    private $cacheDefinedConstants = [];

    /**
     * @param string[] $constantHoldingObjects
     */
    public function __construct(array $constantHoldingObjects = [])
    {
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
        foreach ($this->constantHoldingObjects as $class) {
            if (! class_exists($class)) {
                continue;
            }

            if (! isset($this->cacheDefinedConstants[$class])) {
                $reflectionClass = new ReflectionClass($class);
                $this->cacheDefinedConstants[$class] = $reflectionClass->getReflectionConstants();
            }

            $constants = $this->cacheDefinedConstants[$class];
            $validateConstant = $this->validateConstant($class, $constants, $value);
            if ($validateConstant === []) {
                continue;
            }

            return $validateConstant;
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
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;

class SomeClass
{
    public function run()
    {
        return ComposerJsonSection::REQUIRE;
    }
}
CODE_SAMPLE
                ,
                [
                    'constantHoldingObjects' => [
                        ComposerJsonSection::class,
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param ReflectionClassConstant[] $constants
     * @return string[]
     */
    private function validateConstant(string $class, array $constants, string $value): array
    {
        foreach ($constants as $constant) {
            if (! $constant->isPublic()) {
                continue;
            }

            if ($value === $constant->getValue()) {
                return [sprintf(self::ERROR_MESSAGE, $class, $constant->getName(), $value)];
            }
        }

        return [];
    }
}
