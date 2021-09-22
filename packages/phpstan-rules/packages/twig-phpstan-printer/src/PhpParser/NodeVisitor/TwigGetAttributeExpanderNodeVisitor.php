<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TwigPHPStanPrinter\PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject\VariableAndType;
use Symplify\PHPStanRules\TwigPHPStanPrinter\ObjectTypeMethodAnalyzer;

final class TwigGetAttributeExpanderNodeVisitor extends NodeVisitorAbstract
{
    /**
     * @param VariableAndType[] $variablesAndTypes
     * @param array<string, string> $foreachedVariablesToSingles
     */
    public function __construct(
        private ObjectTypeMethodAnalyzer $objectTypeMethodAnalyzer,
        private array $variablesAndTypes,
        private array $foreachedVariablesToSingles
    ) {
    }

    public function enterNode(Node $node): MethodCall|null
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        if ($node->name instanceof Expr) {
            return null;
        }

        // @see https://github.com/twigphp/Twig/blob/ed29f0010f93df22a96409f5ea442e91728213da/src/Extension/CoreExtension.php#L1378
        $functionName = $node->name->toString();
        if ($functionName !== 'twig_get_attribute') {
            return null;
        }

        // @todo match with provided type
        $variable = $node->args[2]->value;
        if (! $variable instanceof Variable) {
            throw new ShouldNotHappenException();
        }

        if ($variable->name instanceof Expr) {
            return null;
        }

        $variableName = $variable->name;
        $getterMethodName = $this->resolveGetterName($node);

        // @todo correct improve get method, getter property
        $variableType = $this->matchVariableType($variableName);

        // twig can work with 3 magic types: ".name" in twig => "getName()" method, "$name" property and "name()" method in PHP
        if ($variableType instanceof TypeWithClassName) {
            $matchedMethodName = $this->objectTypeMethodAnalyzer->matchObjectTypeGetterName(
                $variableType,
                $getterMethodName
            );
            if ($matchedMethodName) {
                $getterMethodName = $matchedMethodName;
            }
        }

        // @todo add @var type
        // @todo complete args
        return new MethodCall($variable, new Identifier($getterMethodName));
    }

    private function resolveGetterName(FuncCall $funcCall): string
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
}
