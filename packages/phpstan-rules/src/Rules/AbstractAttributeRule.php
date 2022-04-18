<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\PHPStanRules\Contract\AttributeRuleInterface;

/**
 * @template-implements Rule<Node>
 */
abstract class AbstractAttributeRule implements Rule, AttributeRuleInterface
{
    /**
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof Class_ && ! $node instanceof Property && ! $node instanceof ClassMethod && ! $node instanceof Param) {
            return [];
        }

        $errors = [];
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attribute) {
                $currentErrors = $this->processAttribute($attribute, $scope);
                $errors = array_merge($errors, $currentErrors);
            }
        }

        return $errors;
    }
}
