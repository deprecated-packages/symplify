<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\Rules;

use Dibi\Connection;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Nette\Dibi\QueryMasksResolver;
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
    private const MASK_TO_EXPECTED_TYPE = [
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
        private SimpleNameResolver $simpleNameResolver,
        private QueryMasksResolver $queryMasksResolver,
        private ArgTypeResolver $argTypeResolver,
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class, MethodCall::class];
    }

    /**
     * @param Assign|MethodCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof MethodCall) {
            return $this->processMethodCall($node, $scope);
        }

        return [];
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

    private function isDibiConnectionQueryCall(Scope $scope, MethodCall $methodCall): bool
    {
        $callerType = $scope->getType($methodCall->var);

        $dibiConnectionObjectType = new ObjectType(Connection::class);
        if (! $callerType->isSuperTypeOf($dibiConnectionObjectType)->yes()) {
            return false;
        }
        // check direct caller with string masks
        return $this->simpleNameResolver->isNames($methodCall->name, ['query']);
    }

    private function doesMaskMatchType(
        string $queryMask,
        string $mask,
        Type $argumentType,
        mixed $expectedType
    ): bool {
        if ($queryMask !== $mask) {
            return true;
        }
        // is matches => good!
        return is_a($argumentType, $expectedType, true);
    }

    /**
     * @return string[]
     */
    private function processMethodCall(MethodCall $methodCall, Scope $scope): array
    {
        if (! $this->isDibiConnectionQueryCall($scope, $methodCall)) {
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

            foreach (self::MASK_TO_EXPECTED_TYPE as $mask => $expectedType) {
                if ($this->doesMaskMatchType($queryMask, $mask, $argumentType, $expectedType)) {
                    continue;
                }

                $errorMessage = sprintf(self::ERROR_MESSAGE, $queryMask, $argumentType::class, $expectedType);
                $errorMessages[] = $errorMessage;
            }
        }

        return $errorMessages;
    }
}
