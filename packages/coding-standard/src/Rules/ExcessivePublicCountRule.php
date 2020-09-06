<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ExcessivePublicCountRule\ExcessivePublicCountRuleTest
 */
final class ExcessivePublicCountRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Too many public elements on class - %d. Try narrow it down under %d';

    /**
     * @var int
     */
    private $maxPublicClassElementCount;

    public function __construct(int $maxPublicClassElementCount)
    {
        $this->maxPublicClassElementCount = $maxPublicClassElementCount;
    }

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classPublicElementCount = $this->resolveClassPublicElementCount($node, $scope);
        if ($classPublicElementCount < $this->maxPublicClassElementCount) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $classPublicElementCount, $this->maxPublicClassElementCount);
        return [$errorMessage];
    }

    private function resolveClassPublicElementCount(Class_ $class, Scope $scope): int
    {
        $publicElementCount = 0;

        $className = (string) $class->namespacedName;

        foreach ($class->stmts as $classStmt) {
            if (! $classStmt instanceof Property && ! $classStmt instanceof ClassMethod && ! $classStmt instanceof ClassConst) {
                continue;
            }

            if (! $classStmt->isPublic()) {
                continue;
            }

            if (Strings::match($className, '#\bValueObject\b#') && $classStmt instanceof ClassConst) {
                continue;
            }

            ++$publicElementCount;
        }

        return $publicElementCount;
    }
}
