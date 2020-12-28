<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\MethodName;
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
    public const ERROR_MESSAGE = 'Use invokable controller with __invoke() method instead';

    /**
     * @var string
     */
    private const ROUTE_ATTRIBUTE = 'Symfony\Component\Routing\Annotation\Route';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
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
        if (! $this->isInControllerClass($scope)) {
            return [];
        }

        if (! $this->isRouteMethod($node)) {
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

    private function isInControllerClass(Scope $scope): bool
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return false;
        }

        // skip
        if (is_a($className, 'EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController', true)) {
            return false;
        }

        return is_a($className, 'Symfony\Bundle\FrameworkBundle\Controller\AbstractController', true);
    }

    private function isRouteMethod(ClassMethod $node): bool
    {
        if (! $node->isPublic()) {
            return false;
        }

        $docComment = $node->getDocComment();
        if ($docComment !== null) {
            if (Strings::contains($docComment->getText(), '@Route')) {
                return true;
            }
        }

        return $this->hasAttribute($node);
    }

    private function hasAttribute(ClassMethod $node): bool
    {
        /** @var AttributeGroup $attrGroup */
        foreach ((array) $node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $attributeClass = $this->simpleNameResolver->getName($attribute->name);
                if ($attributeClass === self::ROUTE_ATTRIBUTE) {
                    return true;
                }
            }
        }

        return false;
    }
}
