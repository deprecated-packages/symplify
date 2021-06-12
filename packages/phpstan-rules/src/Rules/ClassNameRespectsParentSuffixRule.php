<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Naming\ClassToSuffixResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule\ClassNameRespectsParentSuffixRuleTest
 */
final class ClassNameRespectsParentSuffixRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class should have suffix "%s" to respect parent type';

    /**
     * @var string[]
     */
    private const DEFAULT_PARENT_CLASSES = [
        Command::class,
        EventSubscriberInterface::class,
        AbstractController::class,
        Sniff::class,
        TestCase::class,
    ];

    /**
     * @var string[]
     */
    private array $parentClasses = [];

    /**
     * @param string[] $parentClasses
     */
    public function __construct(
        private ClassToSuffixResolver $classToSuffixResolver,
        private SimpleNameResolver $simpleNameResolver,
        array $parentClasses = []
    ) {
        $this->parentClasses = array_merge($parentClasses, self::DEFAULT_PARENT_CLASSES);
    }

    /**
     * @return array<class-string<Node>>
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
        $className = $this->simpleNameResolver->getName($node);
        if ($className === null) {
            return [];
        }

        if ($node->isAbstract()) {
            return [];
        }

        return $this->processClassNameAndShort($className);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
class Some extends Command
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeCommand extends Command
{
}
CODE_SAMPLE
                ,
                [
                    'parentClasses' => [Command::class],
                ]
            ),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function processClassNameAndShort(string $className): array
    {
        foreach ($this->parentClasses as $parentClass) {
            if (! is_a($className, $parentClass, true)) {
                continue;
            }

            $expectedSuffix = $this->classToSuffixResolver->resolveFromClass($parentClass);
            if (\str_ends_with($className, $expectedSuffix)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $expectedSuffix);
            return [$errorMessage];
        }

        return [];
    }
}
