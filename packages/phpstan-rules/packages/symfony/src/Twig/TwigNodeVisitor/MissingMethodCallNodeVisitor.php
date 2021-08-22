<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig\TwigNodeVisitor;

use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Twig\Environment;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class MissingMethodCallNodeVisitor implements NodeVisitorInterface
{
    /**
     * @var array<string, string[]>
     */
    private array $variableNamesToMissingMethodNames = [];

    /**
     * @param array<string, Type> $variableNamesToTypes
     */
    public function __construct(
        private array $variableNamesToTypes
    ) {
    }

    /**
     * @param Node<Node> $node
     * @return Node<Node>
     */
    public function enterNode(Node $node, Environment $environment): Node
    {
        if (! $node instanceof GetAttrExpression) {
            return $node;
        }

        // at least 3 child nodes:
        // 1st = usually variable name
        // 2nd = method name
        // 3rd...
        $variableName = $this->matchVariableName($node);
        if ($variableName === null) {
            return $node;
        }

        if (! isset($this->variableNamesToTypes[$variableName])) {
            return $node;
        }

        $variableType = $this->variableNamesToTypes[$variableName];
        // @todo add support for iterable variable
        if (! $variableType instanceof TypeWithClassName) {
            return $node;
        }

        // 2. method name
        $methodName = $this->matchMethodName($node);
        if ($methodName === null) {
            return $node;
        }

        $possibleGetterMethodNames = $this->createPossibleMethodNames($methodName);
        if ($this->hasObjectTypeAnyMethod($possibleGetterMethodNames, $variableType)) {
            return $node;
        }

        $this->variableNamesToMissingMethodNames[$variableName][] = $methodName;

        return $node;
    }

    /**
     * @param Node<Node> $node
     * @return Node<Node>|null
     */
    public function leaveNode(Node $node, Environment $environment): ?Node
    {
        return $node;
    }

    public function getPriority(): int
    {
        return 0;
    }

    /**
     * @return array<string, string[]>
     */
    public function getVariableNamesToMissingMethodNames(): array
    {
        return $this->variableNamesToMissingMethodNames;
    }

    /**
     * @return string[]
     */
    private function createPossibleMethodNames(string $methodName): array
    {
        return [
            $methodName,
            'get' . ucfirst($methodName),
            'has' . ucfirst($methodName),
            'is' . ucfirst($methodName),
        ];
    }

    /**
     * @param GetAttrExpression<Node> $getAttrExpression
     */
    private function matchVariableName(GetAttrExpression $getAttrExpression): string|null
    {
        $variableNode = $getAttrExpression->getNode('node');
        if (! $variableNode instanceof NameExpression) {
            return null;
        }

        if (! $variableNode->hasAttribute('name')) {
            return null;
        }

        $variableName = $variableNode->getAttribute('name');
        if (! is_string($variableName)) {
            return null;
        }

        return $variableName;
    }

    /**
     * @param GetAttrExpression<Node> $getAttrExpression
     */
    private function matchMethodName(GetAttrExpression $getAttrExpression): ?string
    {
        $attributeName = $getAttrExpression->getNode('attribute');
        if (! $attributeName instanceof ConstantExpression) {
            return null;
        }

        if (! $attributeName->hasAttribute('value')) {
            return null;
        }

        return $attributeName->getAttribute('value');
    }

    /**
     * @param string[] $possibleGetterMethodNames
     */
    private function hasObjectTypeAnyMethod(
        array $possibleGetterMethodNames,
        TypeWithClassName $typeWithClassName
    ): bool {
        foreach ($possibleGetterMethodNames as $possibleGetterMethodName) {
            if (! $typeWithClassName->hasMethod($possibleGetterMethodName)->yes()) {
                continue;
            }

            return true;
        }

        return false;
    }
}
