<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Enum\MethodName;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\RequireInvokableControllerRule\RequireInvokableControllerRuleTest
 */
final class RequireInvokableControllerRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use invokable controller with __invoke() method instead of named action method';

    public function __construct(
        private SymfonyControllerAnalyzer $symfonyControllerAnalyzer
    ) {
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
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (
            ! $classReflection->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\AbstractController') &&
            ! $classReflection->isSubclassOf('Symfony\Bundle\FrameworkBundle\Controller\Controller')
        ) {
            return [];
        }

        $ruleErrors = [];

        $classLike = $node->getOriginalNode();
        foreach ($classLike->getMethods() as $classMethod) {
            if (! $this->symfonyControllerAnalyzer->isControllerActionMethod($classMethod)) {
                continue;
            }

            if ($classMethod->isMagic()) {
                continue;
            }

            if ($classMethod->name->toString() === MethodName::INVOKE) {
                continue;
            }

            $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($classMethod->getLine())
                ->build();
        }

        return $ruleErrors;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    #[Route()]
    public function someMethod()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

final class SomeController extends AbstractController
{
    #[Route()]
    public function __invoke()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
