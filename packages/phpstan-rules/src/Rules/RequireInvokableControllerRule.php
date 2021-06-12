<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\PHPStanRules\NodeAnalyzer\Symfony\SymfonyControllerAnalyzer;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireInvokableControllerRule\RequireInvokableControllerRuleTest
 */
final class RequireInvokableControllerRule extends AbstractSymplifyRule
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
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->symfonyControllerAnalyzer->isInControllerClass($scope)) {
            return [];
        }

        if (! $this->symfonyControllerAnalyzer->isActionMethod($node)) {
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
    /**
     * @Route()
     */
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
    /**
     * @Route()
     */
    public function __invoke()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
