<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Attribute;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\RequireAttributeNameRuleTest
 */
final class RequireAttributeNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Attribute must have all names explicitly defined';

    public function __construct(
        private AttributeFinder $attributeFinder,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Property::class, Class_::class];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route("/path")]
    public function someAction()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Symfony\Component\Routing\Annotation\Route;

class SomeController
{
    #[Route(path: "/path")]
    public function someAction()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param ClassMethod|Property|Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $attributes = $this->attributeFinder->findAttributes($node);

        foreach ($attributes as $attribute) {
            if ($this->simpleNameResolver->isName($attribute->name, Attribute::class)) {
                continue;
            }

            foreach ($attribute->args as $arg) {
                if ($arg->name !== null) {
                    continue;
                }

                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
