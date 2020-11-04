<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Scalar\DNumber;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use Symplify\PHPStanRules\PhpDoc\BarePhpDocParser;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\MatchingTypeConstantRule\MatchingTypeConstantRuleTest
 */
final class MatchingTypeConstantRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant type should be "%s", but is "%s"';

    /**
     * @var array<string, array<string>>
     */
    private const TYPE_NODES_TO_ACCEPTED_TYPES = [
        LNumber::class => ['int'],
        DNumber::class => ['float', 'double'],
        String_::class => ['string'],
        ConstFetch::class => ['bool'],
    ];

    /**
     * @var array<string, string>
     */
    private const TYPE_CLASS_TO_STRING_TYPE = [
        String_::class => 'string',
        LNumber::class => 'int',
        DNumber::class => 'float',
        ConstFetch::class => 'bool',
    ];

    /**
     * @var BarePhpDocParser
     */
    private $barePhpDocParser;

    public function __construct(BarePhpDocParser $barePhpDocParser)
    {
        $this->barePhpDocParser = $barePhpDocParser;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassConst::class];
    }

    /**
     * @param ClassConst $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkip($node)) {
            return [];
        }

        $type = $this->resolveOnlyVarAnnotationType($node);
        if ($type === null) {
            return [];
        }

        // array, unable to resolve?
        if (Strings::endsWith($type, '[]')) {
            return [];
        }

        $constantValue = $node->consts[0]->value;

        return $this->processConstantValue($constantValue, $type);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var int
     */
    private const LIMIT = 'max';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    /**
     * @var string
     */
    private const LIMIT = 'max';
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkip(ClassConst $classConst): bool
    {
        if ($classConst->getDocComment() === null) {
            return true;
        }

        return count((array) $classConst->consts) !== 1;
    }

    private function resolveOnlyVarAnnotationType(ClassConst $classConst): ?string
    {
        $varTagValueNode = $this->getVarTagValueForNode($classConst);
        if ($varTagValueNode === null) {
            return null;
        }

        return (string) $varTagValueNode->type;
    }

    /**
     * @return string[]
     */
    private function processConstantValue(Expr $expr, string $type): array
    {
        foreach (self::TYPE_NODES_TO_ACCEPTED_TYPES as $typeNode => $acceptedTypes) {
            /** @var string $typeNode */
            if (! is_a($expr, $typeNode, true)) {
                continue;
            }

            if ($this->isValidConstantValue($expr, $type, $acceptedTypes)) {
                return [];
            }

            return $this->reportMissmatch($type, $typeNode);
        }

        return [];
    }

    private function getVarTagValueForNode(Node $node): ?VarTagValueNode
    {
        if ($node->getDocComment() === null) {
            return null;
        }

        $phpDocNode = $this->barePhpDocParser->parseDocBlock($node->getDocComment()->getText());
        return $phpDocNode->getVarTagValues()[0] ?? null;
    }

    /**
     * @param string[] $acceptedTypes
     */
    private function isValidConstantValue(Expr $expr, string $type, array $acceptedTypes): bool
    {
        if (in_array($type, $acceptedTypes, true)) {
            return true;
        }

        // special bool case
        if (! $expr instanceof ConstFetch) {
            return false;
        }

        if ($type !== 'bool') {
            return false;
        }

        return in_array($expr->name->toLowerString(), ['false', 'true'], true);
    }

    /**
     * @return string[]
     */
    private function reportMissmatch(string $expectedType, string $typeNodeClass): array
    {
        $message = sprintf(self::ERROR_MESSAGE, $expectedType, $this->getStringFromNodeClass($typeNodeClass));

        return [$message];
    }

    private function getStringFromNodeClass(string $nodeClass): string
    {
        foreach (self::TYPE_CLASS_TO_STRING_TYPE as $typeClass => $stringType) {
            /** @var string $typeClass */
            if (! is_a($nodeClass, $typeClass, true)) {
                continue;
            }

            return $stringType;
        }

        return $nodeClass;
    }
}
