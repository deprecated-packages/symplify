<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Exception;
use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPUnit\Framework\TestCase;
use Rector\Core\Rector\AbstractRector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PHPStanRules\Naming\ClassToSuffixResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ClassNameRespectsParentSuffixRule\ClassNameRespectsParentSuffixRuleTest
 */
final class ClassNameRespectsParentSuffixRule implements Rule, DocumentedRuleInterface, ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class should have suffix "%s" to respect parent type';

    /**
     * @var class-string[]
     */
    private const DEFAULT_PARENT_CLASSES = [
        Command::class,
        EventSubscriberInterface::class,
        AbstractController::class,
        Sniff::class,
        TestCase::class,
        Exception::class,
        FixerInterface::class,
        Rule::class,
        AbstractRector::class,
    ];

    /**
     * @var class-string[]
     */
    private array $parentClasses = [];

    /**
     * @param class-string[] $parentClasses
     */
    public function __construct(
        private ClassToSuffixResolver $classToSuffixResolver,
        array $parentClasses = [],
    ) {
        $this->parentClasses = array_merge($parentClasses, self::DEFAULT_PARENT_CLASSES);
    }

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @param InClassNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (! $classLike instanceof Class_) {
            return [];
        }

        $classReflection = $node->getClassReflection();
        if ($classReflection->isAbstract()) {
            return [];
        }

        if ($classReflection->isAnonymous()) {
            return [];
        }

        return $this->processClassNameAndShort($classReflection);
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
    private function processClassNameAndShort(ClassReflection $classReflection): array
    {
        foreach ($this->parentClasses as $parentClass) {
            if (! $classReflection->isSubclassOf($parentClass)) {
                continue;
            }

            $expectedSuffix = $this->classToSuffixResolver->resolveFromClass($parentClass);
            if (\str_ends_with($classReflection->getName(), $expectedSuffix)) {
                return [];
            }

            $errorMessage = sprintf(self::ERROR_MESSAGE, $expectedSuffix);
            return [$errorMessage];
        }

        return [];
    }
}
