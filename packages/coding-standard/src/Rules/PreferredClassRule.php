<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\PreferredClassRule\PreferredClassRuleTest
 */
final class PreferredClassRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of "%s" use "%s"';

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
        return [New_::class, Name::class, Class_::class];
    }

    /**
     * @param New_|Name|Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof New_) {
            return $this->processNew($node);
        }

        if ($node instanceof Class_) {
            return $this->processClass($node);
        }

        $class = $node->toString();

        return $this->processClassName($class);
    }

    /**
     * @return string[]
     */
    private function processNew(New_ $new): array
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

        return $this->processClassName($className);
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

            $errorMesage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            return [$errorMesage];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function processClassName(string $className): array
    {
        foreach ($this->oldToPrefferedClasses as $oldClass => $prefferedClass) {
            if ($className !== $oldClass) {
                continue;
            }

            $errorMesage = sprintf(self::ERROR_MESSAGE, $oldClass, $prefferedClass);
            return [$errorMesage];
        }

        return [];
    }
}
