<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use JsonSerializable;
use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\Rule;
use Serializable;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\ValueObjectOverArrayShapeRule\ValueObjectOverArrayShapeRuleTest
 */
final class ValueObjectOverArrayShapeRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Instead of array shape, use value object with specific types in constructor and getters';

    /**
     * @var string
     * @see https://regex101.com/r/04AwRj/1
     */
    private const ARRAY_SHAPE_REGEX = '#array\{(.*?)\}#';

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
     * @return class-string<Node>
     */
    public function getNodeType(): string
    {
        return FunctionLike::class;
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

        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        $match = Strings::match($docComment->getText(), self::ARRAY_SHAPE_REGEX);
        if ($match === null) {
            return [];
        }

        // constructor is allowed, as API entrance
        if ($node instanceof ClassMethod && $node->isMagic()) {
            return [];
        }

        if ($this->isSerializableObject($scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
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
