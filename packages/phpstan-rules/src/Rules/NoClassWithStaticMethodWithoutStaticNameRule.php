<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\NoClassWithStaticMethodWithoutStaticNameRuleTest
 */
final class NoClassWithStaticMethodWithoutStaticNameRule extends AbstractSymplifyRule
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
     * @var string
     * @see https://regex101.com/r/O2LN6F/1
     */
    private const STATIC_REGEX = '#static#i';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
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
        // skip anonymous class
        $shortClassName = $node->name;
        if ($shortClassName === null) {
            return [];
        }

        if (! $this->isClassWithStaticMethod($node)) {
            return [];
        }

        $classShortName = (string) $shortClassName;
        if ($this->shouldSkipClassName($classShortName)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $classShortName);

        return [$errorMessage];
    }

    private function isClassWithStaticMethod($node): bool
    {
        $classMethods = $node->getMethods();
        foreach ($classMethods as $classMethod) {
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

        return (bool) Strings::match($classShortName, self::STATIC_REGEX);
    }

    private function isStaticConstructorOfValueObject(ClassMethod $classMethod): bool
    {
        return (bool) $this->nodeFinder->findFirst((array) $classMethod->stmts, function (Node $node): bool {
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
