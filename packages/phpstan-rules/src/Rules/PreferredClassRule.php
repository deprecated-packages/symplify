<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Instanceof_;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use SplFileInfo;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\PreferredClassRuleTest
 */
final class PreferredClassRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "%s" class/interface use "%s"';

    /**
     * @param string[] $oldToPreferredClasses
     */
    public function __construct(
        private array $oldToPreferredClasses
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [New_::class, Name::class, InClassNode::class, StaticCall::class, Instanceof_::class];
    }

    /**
     * @param New_|Name|InClassNode|StaticCall|Instanceof_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof New_) {
            return $this->processNew($node);
        }

        if ($node instanceof InClassNode) {
            return $this->processClass($node);
        }

        if ($node instanceof StaticCall || $node instanceof Instanceof_) {
            return $this->processExprWithClass($node);
        }

        return $this->processClassName($node->toString());
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
        return new SplFileInfo('...');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        return new CustomFileInfo('...');
    }
}
CODE_SAMPLE
                ,
                [
                    'oldToPreferredClasses' => [
                        SplFileInfo::class => 'CustomFileInfo',
                    ],
                ]
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function processNew(New_ $new): array
    {
        if (! $new->class instanceof Name) {
            return [];
        }

        $className = $new->class->toString();
        return $this->processClassName($className);
    }

    /**
     * @return string[]
     */
    private function processClass(InClassNode $inClassNode): array
    {
        $classReflection = $inClassNode->getClassReflection();

        $parentClassReflection = $classReflection->getParentClass();
        if (! $parentClassReflection instanceof ClassReflection) {
            return [];
        }

        $className = $classReflection->getName();

        $parentClassName = $parentClassReflection->getName();
        foreach ($this->oldToPreferredClasses as $oldClass => $prefferedClass) {
            if ($parentClassName !== $oldClass) {
                continue;
            }

            // check special case, when new class is actually the one we use
            if ($prefferedClass === $className) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            return [$errorMessage];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function processClassName(string $className): array
    {
        foreach ($this->oldToPreferredClasses as $oldClass => $prefferedClass) {
            if ($className !== $oldClass) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            return [$errorMessage];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function processExprWithClass(StaticCall|Instanceof_ $node): array
    {
        if ($node->class instanceof Expr) {
            return [];
        }

        $className = (string) $node->class;
        return $this->processClassName($className);
    }
}
