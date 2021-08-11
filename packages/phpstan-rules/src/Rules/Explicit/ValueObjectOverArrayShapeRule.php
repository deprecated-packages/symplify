<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use JsonSerializable;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\Reflection\ClassReflection;
use Serializable;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SimplePhpDocParser\PhpDocNodeTraverser;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;
use Symplify\SimplePhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\ValueObjectOverArrayShapeRuleTest
 */
final class ValueObjectOverArrayShapeRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of array shape, use value object with specific types in constructor and getters';

    public function __construct(
        private SimplePhpDocParser $simplePhpDocParser,
        private PhpDocNodeTraverser $phpDocNodeTraverser
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        // @see https://stitcher.io/blog/php-8-named-arguments#named-arguments-in-depth
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
/**
 * @return array{line: int}
 */
function createConfiguration()
{
    return ['line' => 100];
}
CODE_SAMPLE
             ,
                <<<'CODE_SAMPLE'
/**
 * @return array{line: int}
 */
function createConfiguration()
{
    return new Configuration(100);
}

final class Configuration
{
    public function __construct(
        private int $line
    ) {
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class, Function_::class];
    }

    /**
     * @param ClassMethod|Function_ $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $simplePhpDocNode = $this->simplePhpDocParser->parseNode($node);
        if (! $simplePhpDocNode instanceof SimplePhpDocNode) {
            return [];
        }

        if (! $this->hasArrayShapeNode($simplePhpDocNode)) {
            return [];
        }

        if ($this->isSerializableObject($scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function hasArrayShapeNode(SimplePhpDocNode $simplePhpDocNode): bool
    {
        $hasArrayShapeNode = false;

        $this->phpDocNodeTraverser->traverseWithCallable($simplePhpDocNode, '', function ($node) use (
            &$hasArrayShapeNode
        ) {
            if ($node instanceof ArrayShapeNode) {
                $hasArrayShapeNode = true;
                return PhpDocNodeTraverser::STOP_TRAVERSAL;
            }

            return $node;
        });

        return $hasArrayShapeNode;
    }

    private function isSerializableObject(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if ($classReflection->implementsInterface(Serializable::class)) {
            return true;
        }

        return $classReflection->implementsInterface(JsonSerializable::class);
    }
}
