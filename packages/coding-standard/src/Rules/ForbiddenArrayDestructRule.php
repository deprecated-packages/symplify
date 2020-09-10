<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Type\ObjectType;
use ReflectionClass;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenArrayDestructRule\ForbiddenArrayDestructRuleTest
 */
final class ForbiddenArrayDestructRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Array destruct is not allowed. Use value object to pass data instead';

    /**
     * @var string
     */
    public const VENDOR_DIRECTORY_PATTERN = '#/vendor/#';

    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    public function __construct(NodeNameResolver $nodeNameResolver)
    {
        $this->nodeNameResolver = $nodeNameResolver;
    }

    public function getNodeType(): string
    {
        return Assign::class;
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
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

        $reflectoinClass = new ReflectionClass($callerType->getClassName());
        return (bool) Strings::match($reflectoinClass->getFileName(), self::VENDOR_DIRECTORY_PATTERN);
    }
}
