<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentGuard\ParentClassMethodGuard;
use Symplify\PHPStanRules\TypeAnalyzer\ForbiddenAllowedTypeAnalyzer;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule\ForbiddenNullableReturnRuleTest
 */
final class ForbiddenNullableReturnRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Return type "%s" cannot be nullable';

    /**
     * @var class-string[]
     */
    private array $forbiddenTypes = [];

    /**
     * @var class-string[]
     */
    private array $allowedTypes = [];

    /**
     * @param class-string[] $forbiddenTypes
     * @param class-string[] $allowedTypes
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ParentClassMethodGuard $parentClassMethodGuard,
        private ForbiddenAllowedTypeAnalyzer $forbiddenAllowedTypeAnalyzer,
        array $forbiddenTypes = [],
        array $allowedTypes = []
    ) {
        $this->forbiddenTypes = $forbiddenTypes;
        $this->allowedTypes = $allowedTypes;
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class, Closure::class];
    }

    /**
     * @param ClassMethod|Function_|Closure $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->parentClassMethodGuard->isFunctionLikeProtected($node, $scope)) {
            return [];
        }

        $returnType = $node->returnType;
        if (! $returnType instanceof NullableType) {
            return [];
        }

        $mainReturnType = $this->simpleNameResolver->getName($returnType->type);
        if ($mainReturnType === null) {
            return [];
        }

        if ($this->forbiddenAllowedTypeAnalyzer->shouldSkip(
            $mainReturnType,
            $this->forbiddenTypes,
            $this->allowedTypes
        )) {
            return [];
        }

        $paramName = $this->simpleNameResolver->getName($mainReturnType);
        $errorMessage = sprintf(self::ERROR_MESSAGE, $paramName);
        return [$errorMessage];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new ConfiguredCodeSample(
                <<<'CODE_SAMPLE'
use PhpParser\Node;

class SomeClass
{
    public function run(): ?Node
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use PhpParser\Node;

class SomeClass
{
    public function run(): Node
    {
    }
}
CODE_SAMPLE
                ,
                [
                    'forbiddenTypes' => [Node::class],
                    'allowedTypes' => [String_::class],
                ]
            ),
        ]);
    }
}
