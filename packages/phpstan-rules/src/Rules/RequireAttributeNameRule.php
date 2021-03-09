<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeFinder\AttrFinder;
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

    /**
     * @var AttrFinder
     */
    private $attrFinder;

    public function __construct(AttrFinder $attrFinder)
    {
        $this->attrFinder = $attrFinder;
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
        $attrs = $this->attrFinder->extra($node);
        foreach ($attrs as $attr) {
            foreach ($attr->args as $arg) {
                if ($arg->name !== null) {
                    continue;
                }

                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
