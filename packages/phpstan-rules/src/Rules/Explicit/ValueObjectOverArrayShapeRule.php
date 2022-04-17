<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use JsonSerializable;
use PhpParser\Builder\FunctionLike;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\Node as PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\Reflection\ClassReflection;
use Serializable;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\PhpDocParser\PhpDocNodeTraverser;
use Symplify\Astral\PhpDocParser\SimplePhpDocParser;
use Symplify\Astral\PhpDocParser\ValueObject\Ast\PhpDoc\SimplePhpDocNode;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\ValueObjectOverArrayShapeRuleTest
 */
final class ValueObjectOverArrayShapeRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of array shape, use value object with specific types in constructor and getters';

    public function __construct(
        private SimplePhpDocParser $simplePhpDocParser,
        private PhpDocNodeTraverser $phpDocNodeTraverser,
        private SimpleNameResolver $simpleNameResolver,
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
    public function getNodeType(): string
    {
        return Node\FunctionLike::class;
    }

    /**
     * @param FunctionLike $node
     * @return mixed[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node instanceof ClassMethod && ! $node instanceof Function_) {
            return [];
        }

        $simplePhpDocNode = $this->simplePhpDocParser->parseNode($node);
        if (! $simplePhpDocNode instanceof SimplePhpDocNode) {
            return [];
        }

        // constructor is allowed, as API entrance
        if ($node instanceof ClassMethod && $this->simpleNameResolver->isName($node, MethodName::CONSTRUCTOR)) {
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

        $this->phpDocNodeTraverser->traverseWithCallable(
            $simplePhpDocNode,
            '',
            function (PhpDocNode $phpDocNode) use (&$hasArrayShapeNode): int|PhpDocNode {
                if ($phpDocNode instanceof ArrayShapeNode) {
                    $hasArrayShapeNode = true;
                    return PhpDocNodeTraverser::STOP_TRAVERSAL;
                }

                return $phpDocNode;
            }
        );

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
