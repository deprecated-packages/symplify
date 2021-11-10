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
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
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
     * @var array<string, class-string<Type>>
     */
    private const MASK_TO_EXPECTED_TYPE_CLASS = [
        '%v' => ArrayType::class,
        '%and' => ArrayType::class,
        '%or' => ArrayType::class,
        '%a' => ArrayType::class,
        '%l' => ArrayType::class,
        '%in' => ArrayType::class,
        '%m' => ArrayType::class,
        '%by' => ArrayType::class,
        '%n' => ArrayType::class,
        // non-array types
        '%s' => StringType::class,
        '%sN' => StringType::class,
        '%b' => BooleanType::class,
        '%i' => IntegerType::class,
        '%iN' => IntegerType::class,
        '%f' => FloatType::class,
    ];

    public function __construct(
        private DibiQueryAnalyzer $dibiQueryAnalyzer,
        private QueryMasksResolver $queryMasksResolver,
        private ArgTypeResolver $argTypeResolver,
        private \Symplify\PHPStanRules\Nette\TypeAnalyzer\NonEmptyArrayTypeRemover $nonEmptyArrayTypeRemover
    ) {
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

        // correct union type on non-empty array since PHPStan 1.0
        $valueType = $this->nonEmptyArrayTypeRemover->clean($valueType);

        $errorMessage = $this->matchErrorMessageIfHappens($mask, $valueType);
        if ($errorMessage === null) {
            return [];
        }

        return [$errorMessage];
    }

    private function matchErrorMessageIfHappens(string $queryMask, Type $argumentType): ?string
    {
        $expectedTypeClass = self::MASK_TO_EXPECTED_TYPE_CLASS[$queryMask] ?? null;

        // nothing to verify
        if ($expectedTypeClass === null) {
            return null;
        }

        // is it correct? skip
        if (is_a($argumentType, $expectedTypeClass, true)) {
            return null;
        }

        // create error message
        return sprintf(self::ERROR_MESSAGE, $queryMask, $argumentType::class, $expectedTypeClass);
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
