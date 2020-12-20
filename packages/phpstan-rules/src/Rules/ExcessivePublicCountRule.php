<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExcessivePublicCountRule\ExcessivePublicCountRuleTest
 */
final class ExcessivePublicCountRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Too many public elements on class - %d. Narrow it down under %d';

    /**
     * @var int
     */
    private $maxPublicClassElementCount;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver, int $maxPublicClassElementCount = 10)
    {
        $this->maxPublicClassElementCount = $maxPublicClassElementCount;
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
        $classPublicElementCount = $this->resolveClassPublicElementCount($node);
        if ($classPublicElementCount < $this->maxPublicClassElementCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $classPublicElementCount, $this->maxPublicClassElementCount);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public $one;

    public $two;

    public $three;
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public $one;

    public $two;
}
CODE_SAMPLE
                ,
                [
                    'maxPublicClassElementCount' => 2,
                ]
            ),
        ]);
    }

    private function resolveClassPublicElementCount(Class_ $class): int
    {
        $className = $this->simpleNameResolver->getName($class);
        if ($className === null) {
            return 0;
        }

        $publicElementCount = 0;

        foreach ($class->stmts as $classStmt) {
            if ($this->shouldSkipClassStmt($classStmt, $className)) {
                continue;
            }

            ++$publicElementCount;
        }

        return $publicElementCount;
    }

    private function shouldSkipClassStmt(Stmt $classStmt, string $className): bool
    {
        if (! $classStmt instanceof Property && ! $classStmt instanceof ClassMethod && ! $classStmt instanceof ClassConst) {
            return true;
        }

        if (! $classStmt->isPublic()) {
            return true;
        }

        if (Strings::match($className, Regex::VALUE_OBJECT_REGEX) && $classStmt instanceof ClassConst) {
            return true;
        }

        if ($classStmt instanceof ClassMethod) {
            $methodName = (string) $classStmt->name;
            return Strings::startsWith($methodName, '__');
        }

        return false;
    }
}
