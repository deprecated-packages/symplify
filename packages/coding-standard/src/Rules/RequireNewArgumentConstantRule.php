<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\RequireNewArgumentConstantRule\RequireNewArgumentConstantRuleTest
 */
final class RequireNewArgumentConstantRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'New expression argument on position %d must use constant over value';

    /**
     * @var array<class-string, mixed[]>
     */
    private $constantArgByNewByType = [];

    /**
     * @param array<class-string, mixed[]> $constantArgByNewByType
     */
    public function __construct(array $constantArgByNewByType = [])
    {
        $this->constantArgByNewByType = $constantArgByNewByType;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        return [];
    }
}
