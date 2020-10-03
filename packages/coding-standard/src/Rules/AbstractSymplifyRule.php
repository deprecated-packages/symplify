<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\Contract\ManyNodeRuleInterface;

abstract class AbstractSymplifyRule implements Rule, ManyNodeRuleInterface
{
    public function getShortClassName(Scope $scope): ?string
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return null;
        }

        $className = $classReflection->getName();
        if (! Strings::contains($className, '\\')) {
            return $className;
        }

        return (string) Strings::after($className, '\\', - 1);
    }

    public function getClassName(Scope $scope): ?string
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return null;
        }

        return $classReflection->getName();
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipNode($node)) {
            return [];
        }

        return $this->process($node, $scope);
    }

    private function shouldSkipNode(Node $node): bool
    {
        foreach ($this->getNodeTypes() as $nodeType) {
            if (is_a($node, $nodeType, true)) {
                return false;
            }
        }

        return true;
    }
}
