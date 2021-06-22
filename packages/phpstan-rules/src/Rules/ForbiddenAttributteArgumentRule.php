<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Doctrine\ORM\Mapping\Entity;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenAttributteArgumentRule\ForbiddenAttributteArgumentRuleTest
 */
final class ForbiddenAttributteArgumentRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Attribute key "%s" cannot be used';

    /**
     * @param array<string, string[]> $argumentsByAttributes
     */
    public function __construct(
        private array $argumentsByAttributes,
        private SimpleNameResolver $simpleNameResolver,
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class, Property::class, ClassMethod::class];
    }

    /**
     * @param Class_|Property|ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Attribute[] $attributes */
        $attributes = $this->nodeFinder->findInstanceOf($node, Attribute::class);

        foreach ($attributes as $attribute) {
            foreach ($this->argumentsByAttributes as $attributeName => $argumentNames) {
                if (! $this->simpleNameResolver->isName($attribute->name, $attributeName)) {
                    continue;
                }

                $matchedArgumentName = $this->matchArgumentName($attribute, $argumentNames);
                if ($matchedArgumentName === null) {
                    continue;
                }

                return [sprintf(self::ERROR_MESSAGE, $matchedArgumentName)];
            }
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use Doctrine\ORM\Mapping\Entity;

#[Entity(repositoryClass: SomeRepository::class)]
class SomeClass
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class SomeClass
{
}
CODE_SAMPLE
                ,
                [
                    '$argumentsByAttributes' => [
                        Entity::class => ['repositoryClass'],
                    ],
                ]
            ),
        ]);
    }

    /**
     * @param string[] $argumentNames
     */
    private function isArgumentNameMatch(Arg $arg, array $argumentNames): bool
    {
        if ($arg->name === null) {
            return false;
        }

        $argumentArgName = $this->simpleNameResolver->getName($arg->name);
        return in_array($argumentArgName, $argumentNames, true);
    }

    /**
     * @param string[] $argumentNames
     */
    private function matchArgumentName(Attribute $attribute, array $argumentNames): ?string
    {
        foreach ($attribute->args as $arg) {
            if (! $this->isArgumentNameMatch($arg, $argumentNames)) {
                continue;
            }

            return (string) $arg->name;
        }

        return null;
    }
}
