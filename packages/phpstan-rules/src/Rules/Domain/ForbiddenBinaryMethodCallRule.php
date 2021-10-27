<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Domain;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenBinaryMethodCallRule\ForbiddenBinaryMethodCallRuleTest
 */
final class ForbiddenBinaryMethodCallRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'This call cannot be used in binary compare. Use direct method instead';

    /**
     * @param array<class-string, string[]> $typesToMethods
     */
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private array $typesToMethods
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [MethodCall::class];
    }

    /**
     * @param MethodCall $node
     * @return mixed[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $callerType = $scope->getType($node->var);

        foreach ($this->typesToMethods as $type => $methods) {
            $desiredObjectType = new ObjectType($type);
            if (! $desiredObjectType->isSuperTypeOf($callerType)->yes()) {
                continue;
            }

            if (! $this->simpleNameResolver->isNames($node->name, $methods)) {
                continue;
            }

            $parent = $node->getAttribute(AttributeKey::PARENT);
            if (! $parent instanceof BinaryOp) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            self::ERROR_MESSAGE,
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
$someType = new SomeType();
if ($someType->getId() !== null) {
    return $someType->getId();
}
CODE_SAMPLE
                ,
                    <<<'CODE_SAMPLE'
$someType = new SomeType();
if ($someType->hasId()) {
    return $someType->getId();
}
CODE_SAMPLE
                ,
                    [
                        'SomeType' => ['getId'],
                    ]
                ),
            ]
        );
    }
}
