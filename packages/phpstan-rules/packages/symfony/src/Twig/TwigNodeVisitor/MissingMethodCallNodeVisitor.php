<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\Twig\TwigNodeVisitor;

use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symplify\PHPStanRules\Symfony\ObjectTypeMethodAnalyzer;
use Symplify\PHPStanRules\Symfony\ValueObject\ForeachVariable;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndMissingMethodName;
use Symplify\PHPStanRules\Symfony\ValueObject\VariableAndType;
use Twig\Environment;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\ForNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

final class MissingMethodCallNodeVisitor implements NodeVisitorInterface
{
    /**
     * @var string
     */
    private const NAME = 'name';

    /**
     * @var ForeachVariable[]
     */
    private array $foreachVariables = [];

    /**
     * @var VariableAndMissingMethodName[]
     */
    private array $variableAndMissingMethodNames = [];

    /**
     * @param VariableAndType[] $variableAndTypes
     */
    public function __construct(
        private array $variableAndTypes,
        private ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer
    ) {
    }

    /**
     * @param Node<Node> $node
     * @return Node<Node>
     */
    public function enterNode(Node $node, Environment $environment): Node
    {
        if ($node instanceof ForNode) {
            $this->collectForeachVariable($node);
            return $node;
        }

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

        $variableType = $this->matchVariableType($variableName);
        if (! $variableType instanceof TypeWithClassName) {
            return $node;
        }

        // 2. method name
        $methodName = $this->matchMethodName($node);
        if ($methodName === null) {
            return $node;
        }

        if ($this->objectTypeMethodAnalyzer->hasObjectTypeMagicGetter($variableType, $methodName)) {
            return $node;
        }

        $this->variableAndMissingMethodNames[] = new VariableAndMissingMethodName($variableName, $methodName);

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
     * @return VariableAndMissingMethodName[]
     */
    public function getVariableAndMissingMethodNames(): array
    {
        return $this->variableAndMissingMethodNames;
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

        if (! $variableNode->hasAttribute(self::NAME)) {
            return null;
        }

        $variableName = $variableNode->getAttribute(self::NAME);
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

    private function matchVariableType(string $variableName): Type|null
    {
        foreach ($this->variableAndTypes as $variableAndType) {
            if ($variableAndType->getVariable() === $variableName) {
                return $variableAndType->getType();
            }
        }

        foreach ($this->foreachVariables as $foreachVariable) {
            if ($foreachVariable->getItemName() !== $variableName) {
                continue;
            }

            $arrayType = $this->matchVariableType($foreachVariable->getArrayName());
            if ($arrayType instanceof ArrayType) {
                return $arrayType->getItemType();
            }
        }

        return null;
    }

    /**
     * @param ForNode<Node> $forNode
     */
    private function collectForeachVariable(ForNode $forNode): void
    {
        // 1. iterated node
        $nameExpression = $forNode->getNode('seq');
        $arrayItemName = $nameExpression->getAttribute(self::NAME);

        // 2. assigned node
        $assignNameExpression = $forNode->getNode('value_target');
        if (! $assignNameExpression instanceof AssignNameExpression) {
            return;
        }

        $basicArrayName = $assignNameExpression->getAttribute(self::NAME);
        $this->foreachVariables[] = new ForeachVariable($arrayItemName, $basicArrayName);
    }
}
