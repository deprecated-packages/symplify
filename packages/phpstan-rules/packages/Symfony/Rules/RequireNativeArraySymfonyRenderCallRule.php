<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use PHPStan\Type\ThisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Symfony\Rules\RequireNativeArraySymfonyRenderCallRule\RequireNativeArraySymfonyRenderCallRuleTest
 */
final class RequireNativeArraySymfonyRenderCallRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Second argument of $this->render("template.twig", [...]) method should be explicit array, to avoid accidental variable override, see https://tomasvotruba.com/blog/2021/02/15/how-dangerous-is-your-nette-template-assign/';

    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->name instanceof Identifier) {
            return [];
        }

        if ($node->name->toString() !== 'render') {
            return [];
        }

        $callerType = $scope->getType($node->var);
        if (! $callerType instanceof ThisType) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->isSubclassOf(AbstractController::class)) {
            return [];
        }

        if (count($node->args) !== 2) {
            return [];
        }

        $argOrVariadicPlaceholder = $node->args[1];
        if (! $argOrVariadicPlaceholder instanceof Arg) {
            return [];
        }

        $secondArgValue = $argOrVariadicPlaceholder->value;
        if ($secondArgValue instanceof Array_) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(
            <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    public function default()
    {
        $parameters['name'] = 'John';
        $parameters['name'] = 'Doe';
        return $this->render('...', $parameters);
    }
}
CODE_SAMPLE
            ,
            <<<'CODE_SAMPLE'
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SomeController extends AbstractController
{
    public function default()
    {
        return $this->render('...', [
            'name' => 'John'
        ]);
    }
}
CODE_SAMPLE
        )]);
    }
}
