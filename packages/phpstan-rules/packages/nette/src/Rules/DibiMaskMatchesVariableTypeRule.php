<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;
use Symplify\PHPStanRules\Nette\Dibi\QueryMasksResolver;
use Symplify\PHPStanRules\Nette\NodeAnalyzer\DibiQueryAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\SprintfSpecifierTypeResolver;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\TypeResolver\ArgTypeResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Nette\Tests\Rules\DibiMaskMatchesVariableTypeRule\DibiMaskMatchesVariableTypeRuleTest
 */
final class DibiMaskMatchesVariableTypeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Modifier "%s" is not matching passed variable type "%s". The "%s" type is expected - see https://dibiphp.com/en/documentation#toc-modifiers-for-arrays';

    /**
     * @see https://dibiphp.com/en/documentation#toc-modifiers-for-arrays
     * @var array<string, Type>
     */
    private array $masksToExpectedTypes = [];

    public function __construct(
        private DibiQueryAnalyzer $dibiQueryAnalyzer,
        private QueryMasksResolver $queryMasksResolver,
        private ArgTypeResolver $argTypeResolver,
    ) {
        $arrayType = new ArrayType(new MixedType(), new MixedType());

        $this->masksToExpectedTypes = [
            '%v' => $arrayType,
            '%and' => $arrayType,
            '%or' => $arrayType,
            '%a' => $arrayType,
            '%l' => $arrayType,
            '%in' => $arrayType,
            '%m' => $arrayType,
            '%by' => $arrayType,
            '%n' => $arrayType,
            // non-array types
            '%s' => new StringType(),
            '%sN' => new StringType(),
            '%b' => new BooleanType(),
            '%i' => new IntegerType(),
            '%iN' => new IntegerType(),
            '%f' => new FloatType(),
        ];
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class, MethodCall::class, ArrayItem::class];
    }

    /**
     * @param Assign|MethodCall|ArrayItem $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        // skip itself
        if (in_array($classReflection->getName(), [self::class, SprintfSpecifierTypeResolver::class], true)) {
            return [];
        }

        if ($node instanceof MethodCall) {
            return $this->processMethodCall($node, $scope);
        }

        if ($node instanceof ArrayItem) {
            return $this->processArrayItem($node, $scope);
        }

        return $this->processAssign($node, $scope);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$database->query('INSERT INTO table %v', 'string');
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$database->query('INSERT INTO table %v', ['name' => 'Matthias']);
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function processMethodCall(MethodCall $methodCall, Scope $scope): array
    {
        if (! $this->dibiQueryAnalyzer->isDibiConnectionQueryCall($scope, $methodCall)) {
            return [];
        }

        $queryMasks = $this->queryMasksResolver->resolveQueryMasks($methodCall, $scope);
        $argumentTypes = $this->argTypeResolver->resolveArgTypesWithoutFirst($methodCall, $scope);

        if (count($queryMasks) !== count($argumentTypes)) {
            return [];
        }

        $errorMessages = [];

        foreach ($argumentTypes as $key => $argumentType) {
            $queryMask = $queryMasks[$key];

            $errorMessage = $this->matchErrorMessageIfHappens($queryMask, $argumentType);
            if ($errorMessage === null) {
                continue;
            }

            $errorMessages[] = $errorMessage;
        }

        return $errorMessages;
    }

    /**
     * @return string[]
     */
    private function processArrayItem(ArrayItem $arrayItem, Scope $scope): array
    {
        $mask = $this->queryMasksResolver->resolveSingleQueryMask($arrayItem->key, $scope);
        if ($mask === null) {
            return [];
        }

        $valueType = $scope->getType($arrayItem->value);

        $errorMessage = $this->matchErrorMessageIfHappens($mask, $valueType);
        if ($errorMessage === null) {
            return [];
        }

        return [$errorMessage];
    }

    private function matchErrorMessageIfHappens(string $queryMask, Type $argumentType): ?string
    {
        $expectedType = $this->masksToExpectedTypes[$queryMask] ?? null;

        // nothing to verify
        if (! $expectedType instanceof Type) {
            return null;
        }

        if ($expectedType->isSuperTypeOf($argumentType)->yes()) {
            return null;
        }

        return sprintf(
            self::ERROR_MESSAGE,
            $queryMask,
            $argumentType->describe(VerbosityLevel::typeOnly()),
            $expectedType->describe(VerbosityLevel::typeOnly())
        );
    }

    /**
     * @return string[]
     */
    private function processAssign(Assign $assign, Scope $scope): array
    {
        if (! $assign->var instanceof ArrayDimFetch) {
            return [];
        }

        $arrayDimFetch = $assign->var;

        $queryMask = $this->queryMasksResolver->resolveSingleQueryMask($arrayDimFetch->dim, $scope);
        if ($queryMask === null) {
            return [];
        }

        $exprType = $scope->getType($assign->expr);

        $errorMessage = $this->matchErrorMessageIfHappens($queryMask, $exprType);
        if ($errorMessage === null) {
            return [];
        }

        return [$errorMessage];
    }
}
