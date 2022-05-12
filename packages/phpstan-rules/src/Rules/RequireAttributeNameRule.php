<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Attribute;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\NodeAnalyzer\AttributeFinder;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireAttributeNameRule\RequireAttributeNameRuleTest
 */
final class RequireAttributeNameRule implements Rule, DocumentedRuleInterface
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

    public function getNodeType(): string
    {
        return Class_::class;
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
     * @param Class_ $node
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $attributes = $this->attributeFinder->findInClass($node);

        $ruleErrors = [];

        foreach ($attributes as $attribute) {
            if ($this->simpleNameResolver->isName($attribute->name, Attribute::class)) {
                continue;
            }

            foreach ($attribute->args as $arg) {
                if ($arg->name !== null) {
                    continue;
                }

                $ruleErrors[] = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                    ->line($attribute->getLine())
                    ->build();
            }
        }

        return $ruleErrors;
    }
}
