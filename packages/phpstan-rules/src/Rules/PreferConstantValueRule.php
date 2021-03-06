<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use ReflectionClass;
use ReflectionClassConstant;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
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
     * @var array<string, array<string, string>>
     */
    private $constantHoldingObjects = [];

    /**
     * @var array<string, ReflectionClassConstant[]>
     */
    private $cacheDefinedConstants = [];

    /**
     * @param array<string, array<string, string>> $constantHoldingObjects
     */
    public function __construct(array $constantHoldingObjects = [])
    {
        $this->constantHoldingObjects = $constantHoldingObjects;
    }

    /**
     * @return array<class-string<Node>>
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
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if ($parent instanceof Const_) {
            return [];
        }

        foreach ($this->constantHoldingObjects as $class => $constants) {
            if (! isset($this->cacheDefinedConstants[$class])) {
                $this->collectConstants($class, $constants);
            }

            $constants = $this->cacheDefinedConstants[$class];
            $validateConstant = $this->validateConstant($class, $constants, $value);
            if ($validateConstant !== []) {
                return $validateConstant;
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
                        ComposerJsonSection::class => ['REQUIRE(_.*)?', 'AUTOLOAD(_.*)?'],
                    ],
                ]
            ),
        ]);
    }

    private function collectConstants(string $class, array $constants): void
    {
        $this->cacheDefinedConstants[$class] = [];

        if (! class_exists($class)) {
            return;
        }

        $reflectionClass = new ReflectionClass($class);
        $definedConstants = $reflectionClass->getConstants();

        foreach ($constants as $constant) {
            $constantNames = array_keys($definedConstants);
            foreach ($constantNames as $constantName) {
                if (Strings::match($constantName, '#^' . $constant . '$#')) {
                    $this->cacheDefinedConstants[$class][] = $reflectionClass->getReflectionConstant($constantName);
                }
            }
        }
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
