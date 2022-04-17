<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\PHPStanRules\Symfony\NodeAnalyzer\SymfonyControllerAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Symfony\Tests\Rules\RequireInvokableControllerRule\RequireInvokableControllerRuleTest
 */
final class RequireInvokableControllerRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
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
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->symfonyControllerAnalyzer->isInControllerClass($scope)) {
            return [];
        }

        if (! $this->symfonyControllerAnalyzer->isControllerActionMethod($node)) {
            return [];
        }

        $classMethodName = (string) $node->name;
        if ($classMethodName === MethodName::INVOKE) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
