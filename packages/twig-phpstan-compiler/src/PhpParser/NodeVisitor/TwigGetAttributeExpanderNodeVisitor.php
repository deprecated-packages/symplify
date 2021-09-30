<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\TemplatePHPStanCompiler\ValueObject\VariableAndType;
use Symplify\TwigPHPStanCompiler\ObjectTypeMethodAnalyzer;

final class TwigGetAttributeExpanderNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param array<string, string> $foreachedVariablesToSingles
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer,
        private array $variablesAndTypes,
        private array $foreachedVariablesToSingles
    ) {
    }

    public function enterNode(Node $node): Expr|null
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if ($node->name instanceof Expr) {
            return null;
        }

        // @see https://github.com/twigphp/Twig/blob/ed29f0010f93df22a96409f5ea442e91728213da/src/Extension/CoreExtension.php#L1378

        if (! $this->simpleNameResolver->isName($node, 'twig_get_attribute')) {
            return null;
        }

        $variableName = $this->resolveVariableName($node);
        if ($variableName === null) {
            return null;
        }

        $accessorName = $this->resolveAccessor($node);

        // @todo correct improve get method, getter property
        $variableType = $this->matchVariableType($variableName);

        if (! $variableType instanceof Type) {
            // dummy fallback
            return new MethodCall(new Variable($variableName), new Identifier($accessorName));
        }

        if ($variableType->isOffsetAccessible()->yes()) {
            // array access safe fallback?
            return new ArrayDimFetch(new Variable($variableName), new String_($accessorName));
        }

        if ($this->hasPublicProperty($variableType, $accessorName)) {
            return new PropertyFetch(new Variable($variableName), new Identifier($accessorName));
        }

        return $this->resolveMethodCall($accessorName, $variableType, $variableName);
    }

    private function resolveAccessor(FuncCall $funcCall): string
    {
        $string = $funcCall->args[3]->value;
        if (! $string instanceof String_) {
            throw new ShouldNotHappenException();
        }

        return $string->value;
    }

    private function matchVariableType(string $variableName): ?Type
    {
        foreach ($this->variablesAndTypes as $variablesAndType) {
            if ($variablesAndType->getVariable() !== $variableName) {
                continue;
            }

            return $variablesAndType->getType();
        }

        return $this->matchForeachVariableType();
    }

    private function matchForeachVariableType(): ?Type
    {
        // foreached variable
        foreach ($this->variablesAndTypes as $variablesAndType) {
            foreach (array_keys($this->foreachedVariablesToSingles) as $foreachedVariable) {
                if ($foreachedVariable !== $variablesAndType->getVariable()) {
                    continue;
                }

                $possibleArrayType = $variablesAndType->getType();
                if (! $possibleArrayType instanceof ArrayType) {
                    continue;
                }

                return $possibleArrayType->getItemType();
            }
        }

        return null;
    }

    private function resolveVariableName(FuncCall $funcCall): string|null
    {
        // @todo match with provided type
        $variable = $funcCall->args[2]->value;
        if (! $variable instanceof Variable) {
            throw new ShouldNotHappenException();
        }

        if ($variable->name instanceof Expr) {
            return null;
        }

        return $variable->name;
    }

    private function resolveMethodCall(
        string $accessorName,
        Type $variableType,
        string $variableName
    ): MethodCall {
        $matchedMethodName = $accessorName;

        // twig can work with 3 magic types: ".name" in twig => "getName()" method, "$name" property and "name()" method in PHP
        if ($variableType instanceof TypeWithClassName) {
            $resolvedGetterMethodName = $this->objectTypeMethodAnalyzer->matchObjectTypeGetterName(
                $variableType,
                $accessorName
            );

            if ($resolvedGetterMethodName) {
                $matchedMethodName = $resolvedGetterMethodName;
            }
        }

        return new MethodCall(new Variable($variableName), new Identifier($matchedMethodName));
    }

    private function hasPublicProperty(Type $type, string $variableName): bool
    {
        if (! $type instanceof TypeWithClassName) {
            return false;
        }

        if (! $type->hasProperty($variableName)->yes()) {
            return false;
        }

        return property_exists($type->getClassName(), $variableName);
    }
}
