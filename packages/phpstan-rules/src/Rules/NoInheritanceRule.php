<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * It's faster
 * @see https://twitter.com/nicolasgrekas/status/1357743051905654786
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NoInheritanceRule\NoInheritanceRuleTest
 */
final class NoInheritanceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not inherit from abstract class, better use composition';

    /**
     * @var array<class-string>
     */
    private const ALLOWED_PARENT_TYPES = [
        // possibly configurable?
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'Symplify\PHPStanRules\Rules\AbstractSymplifyRule',


        'Symfony\Component\HttpKernel\KernelInterface',
        'Symfony\Component\HttpKernel\Bundle\Bundle',
        'PHPUnit\Framework\TestCase',
        'Throwable',
        'Symfony\Component\Console\Application',
        'Symfony\Component\Console\Command\Command',
        'Symfony\Component\DependencyInjection\Extension\Extension',
        'PhpParser\NodeTraverser',
        'PhpParser\NodeVisitor',
        'PhpParser\PrettyPrinter\Standard',
        'SplFileInfo',
    ];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var TypeChecker
     */
    private $typeChecker;

    public function __construct(SimpleNameResolver $simpleNameResolver, TypeChecker $typeChecker)
    {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->typeChecker = $typeChecker;
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
        if ($node->extends === null) {
            return [];
        }

        $parentClassName = $this->simpleNameResolver->getName($node->extends);
        if ($parentClassName === null) {
            return [];
        }

        if ($this->typeChecker->isInstanceOf($parentClassName, self::ALLOWED_PARENT_TYPES)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass extends AbstratcClass
{
    public function run()
    {
        $this->parentMethod();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private function __construct(
        private $dependency Dependency
    ) {
    }

    public function run()
    {
        $this->dependency->otherMethod();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
