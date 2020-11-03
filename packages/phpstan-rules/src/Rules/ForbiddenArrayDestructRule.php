<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use ReflectionClass;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenArrayDestructRule\ForbiddenArrayDestructRuleTest
 */
final class ForbiddenArrayDestructRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array destruct is not allowed. Use value object to pass data instead';

    /**
     * @var string
     * @see https://regex101.com/r/dhGhYp/1
     */
    public const VENDOR_DIRECTORY_REGEX = '#/vendor/#';

    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $node->var instanceof Array_) {
            return [];
        }

        // swaps are allowed
        if ($node->expr instanceof Array_) {
            return [];
        }

        if ($this->isAllowedCall($node)) {
            return [];
        }

        // is 3rd party method call → nothing we can do about it
        if ($this->isVendorProvider($node, $scope)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isAllowedCall(Assign $assign): bool
    {
        // "explode()" is allowed
        if ($assign->expr instanceof FuncCall && $this->nodeNameResolver->isName($assign->expr->name, 'explode')) {
            return true;
        }

        // Strings::split() is allowed
        return $assign->expr instanceof StaticCall && $this->nodeNameResolver->isName($assign->expr->name, 'split');
    }

    private function isVendorProvider(Assign $assign, Scope $scope): bool
    {
        if (! $assign->expr instanceof MethodCall) {
            return false;
        }

        $callerType = $scope->getType($assign->expr->var);
        if (! $callerType instanceof ObjectType) {
            return false;
        }

        $reflectionClass = new ReflectionClass($callerType->getClassName());
        return (bool) Strings::match((string) $reflectionClass->getFileName(), self::VENDOR_DIRECTORY_REGEX);
    }
}
