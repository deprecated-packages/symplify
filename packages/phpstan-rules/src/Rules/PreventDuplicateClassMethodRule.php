<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\PreventDuplicateClassMethodRuleTest
 */
final class PreventDuplicateClassMethodRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Content of method "%s" is duplicated with method in "%s" class, use unique content instead';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var Standard
     */
    private $printerStandard;

    /**
     * @var string
     */
    private $firstClassByName = [];

    /**
     * @var array<string, string>
     */
    private $contentMethodByName = [];

    public function __construct(SimpleNameResolver $simpleNameResolver, Standard $printerStandard)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->printerStandard = $printerStandard;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Class_|null */
        $class = $this->resolveCurrentClass($node);

        if ($class === null) {
            return true;
        }

        $className = property_exists($class, 'namespacedName')
            ? $class->namespacedName->toString()
            : $class->name->toString();

        if ($this->isConstructorOrInTestClass($node, $className)) {
            return [];
        }

        if (! $node->isPublic()) {
            return [];
        }

        $classMethodName = $node->name->toString();
        $printStmts = $this->printerStandard->prettyPrint($node->stmts);
        if (! isset($this->contentMethodByName[$classMethodName])) {
            $this->firstClassByName[$classMethodName] = $className;
            $this->contentMethodByName[$classMethodName] = $printStmts;
            return [];
        }

        if ($printStmts !== $this->contentMethodByName[$classMethodName]) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $classMethodName, $this->firstClassByName[$classMethodName])];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class A
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class A
{
    public function someMethod()
    {
        (new SmartFinder())->run('.php');
    }
}

class B
{
    public function someMethod()
    {
        (new SmartFinder())->run('.js');
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isConstructorOrInTestClass(ClassMethod $classMethod, string $className): bool
    {
        if ($this->simpleNameResolver->isName($classMethod->name, MethodName::CONSTRUCTOR)) {
            return true;
        }

        if (Strings::endsWith($className, 'Test')) {
            return true;
        }

        return false;
    }
}
