<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\NoClassWithStaticMethodWithoutStaticNameRuleTest
 */
final class NoClassWithStaticMethodWithoutStaticNameRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class "%s" with static method must have "static" in its name';

    /**
     * @var string[]
     */
    private const ALLOWED_CLASS_NAMES = [
        // symfony classes with static methods
        '#Subscriber$#',
        '#Command$#',
    ];

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct()
    {
        $this->nodeFinder = new NodeFinder();
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
        // skip anonymous class
        if ($node->name === null) {
            return [];
        }

        if (! $this->isClassWithStaticMethod($node)) {
            return [];
        }

        $classShortName = (string) $node->name;
        if ($this->shouldSkipClassName($classShortName)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $classShortName)];
    }

    private function isClassWithStaticMethod($node): bool
    {
        foreach ($node->getMethods() as $classMethod) {
            if ($classMethod->isStatic()) {
                if ($this->isStaticConstructorOfValueObject($classMethod)) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    private function shouldSkipClassName(string $classShortName): bool
    {
        foreach (self::ALLOWED_CLASS_NAMES as $allowedClassName) {
            if (Strings::match($classShortName, $allowedClassName)) {
                return true;
            }
        }

        return (bool) Strings::match($classShortName, '#static#i');
    }

    private function isStaticConstructorOfValueObject(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirst((array) $classMethod->stmts, function (Node $node) {
            if (! $node instanceof Return_) {
                return false;
            }

            if (! $node->expr instanceof New_) {
                return false;
            }

            /** @var New_ $new */
            $new = $node->expr;
            if (! $new->class instanceof Name) {
                return false;
            }

            return $new->class->toString() === 'self';
        });
    }
}
