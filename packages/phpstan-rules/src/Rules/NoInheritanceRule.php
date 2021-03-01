<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Php\TypeChecker;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * It's faster
 * @see https://twitter.com/nicolasgrekas/status/1357743051905654786
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\NoInheritanceRule\NoInheritanceRuleTest
 */
final class NoInheritanceRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not inherit from abstract class, better use composition';

    /**
     * @var array<class-string>
     */
    private const DEFAULT_ALLOWED_PARENT_TYPES = [
        'Symfony\Component\HttpKernel\KernelInterface',
        'Symfony\Component\HttpKernel\Bundle\Bundle',
        'Symfony\Component\Console\Application',
        'Symfony\Component\Console\Command\Command',
        'Symfony\Component\Console\Style\SymfonyStyle',
        'Symfony\Component\DependencyInjection\Extension\Extension',
        'Symfony\Component\DependencyInjection\Loader\FileLoader',
        'Symfony\Contracts\EventDispatcher\Event',
        'Symfony\Component\Filesystem\Filesystem',
        'Twig\Extension\ExtensionInterface',
        'PhpCsFixer\AbstractDoctrineAnnotationFixer',
        'PhpParser\NodeTraverser',
        'PhpParser\Builder',
        'PhpParser\PrettyPrinter\Standard',
        'PHPStan\PhpDocParser\Ast\Node',
        'PHPUnit\Framework\TestCase',
        'SplFileInfo',
        'Throwable',
    ];

    /**
     * @var string[]
     */
    private const DEFAULT_ALLOWED_DIRECT_PARENT_TYPES = ['PhpParser\NodeVisitorAbstract'];

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var TypeChecker
     */
    private $typeChecker;

    /**
     * @var array<class-string>
     */
    private $allowedParentTypes = [];

    /**
     * @param array<class-string> $allowedParentTypes
     */
    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        TypeChecker $typeChecker,
        array $allowedParentTypes
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->typeChecker = $typeChecker;
        $this->allowedParentTypes = array_merge(self::DEFAULT_ALLOWED_PARENT_TYPES, $allowedParentTypes);
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

        if ($this->typeChecker->isInstanceOf($parentClassName, $this->allowedParentTypes)) {
            return [];
        }

        $parentClass = $this->simpleNameResolver->getName($node->extends);
        foreach (self::DEFAULT_ALLOWED_DIRECT_PARENT_TYPES as $allowedDirectParentType) {
            if ($allowedDirectParentType === $parentClass) {
                return [];
            }
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
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
                ,
                [
                    'allowedParentTypes' => ['AnotherParent'],
                ]
            ),
        ]);
    }
}
