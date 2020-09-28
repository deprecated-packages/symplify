<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredClassRule\PreferredClassRuleTest
 */
final class PreferredClassRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "%s" class/interface use "%s"';

    /**
     * @var string[]
     */
    private $oldToPrefferedClasses = [];

    /**
     * @param string[] $oldToPrefferedClasses
     */
    public function __construct(array $oldToPrefferedClasses)
    {
        $this->oldToPrefferedClasses = $oldToPrefferedClasses;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class, Name::class, Class_::class, StaticCall::class];
    }

    /**
     * @param New_|Name|Class_|StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof New_) {
            return $this->processNew($node, $scope);
        }

        if ($node instanceof Class_) {
            return $this->processClass($node);
        }

        if ($node instanceof StaticCall) {
            return $this->processStaticCall($node, $scope);
        }

        return $this->processClassName($node->toString(), $node, $scope);
    }

    /**
     * @return string[]
     */
    private function processNew(New_ $new, Scope $scope): array
    {
        $newClass = $new->class;
        if ($newClass instanceof Expr) {
            return [];
        }

        if ($newClass instanceof Class_) {
            $className = $newClass->name;
            if ($className === null) {
                return [];
            }
            $className = $className->toString();
        } else {
            $className = (string) $newClass;
        }

        return $this->processClassName($className, $new, $scope);
    }

    /**
     * @return string[]
     */
    private function processClass(Class_ $class): array
    {
        if ($class->extends === null) {
            return [];
        }

        $parentClass = $class->extends->toString();
        foreach ($this->oldToPrefferedClasses as $oldClass => $prefferedClass) {
            if ($parentClass !== $oldClass) {
                continue;
            }

            // check special case, when new class is actually the one we use
            if ($prefferedClass === (string) $class->namespacedName) {
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
    private function processClassName(string $className, Node $node, Scope $scope): array
    {
        if ($this->isTypeRequiredByParentClassOrContract($node, $scope)) {
            return [];
        }

        foreach ($this->oldToPrefferedClasses as $oldClass => $prefferedClass) {
            if ($className !== $oldClass) {
                continue;
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            return [$errorMessage];
        }

        return [];
    }

    private function isTypeRequiredByParentClassOrContract(Node $node, Scope $scope): bool
    {
        $parent = $node->getAttribute('parent');
        if (! $parent instanceof Param) {
            return false;
        }

        // possibly protected by parent class
        $parentParent = $parent->getAttribute('parent');
        if (! $parentParent instanceof ClassMethod) {
            return false;
        }

        /** @var string $methodName */
        $methodName = (string) $parentParent->name;

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        $parentClassLikes = array_merge($classReflection->getInterfaces(), $classReflection->getParents());

        foreach ($parentClassLikes as $parentClassLike) {
            if ($parentClassLike->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    private function processStaticCall(StaticCall $staticCall, Scope $scope): array
    {
        if ($staticCall->class instanceof Expr) {
            return [];
        }

        $className = (string) $staticCall->class;

        return $this->processClassName($className, $staticCall, $scope);
    }
}
