<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoEmptyRule\NoEmptyRuleTest
 */
final class NoStaticCallRule extends AbstractManyNodeTypeRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use static calls';

    /**
     * @var string[]
     */
    private const DEFAULT_ALLOWED_STATIC_CALL_CLASSES = ['Nette\Utils\Strings', 'Nette\Utils\DateTime'];

    /**
     * @var string[]
     */
    private $allowedStaticCallClasses = [];

    /**
     * @param string[] $allowedStaticCallClasses
     */
    public function __construct(array $allowedStaticCallClasses)
    {
        $this->allowedStaticCallClasses = array_merge(
            $allowedStaticCallClasses,
            self::DEFAULT_ALLOWED_STATIC_CALL_CLASSES
        );
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->class instanceof Expr) {
            return [];
        }

        $className = (string) $node->class;

        if (in_array($className, ['self', 'parent', 'static'], true)) {
            return [];
        }

        if (in_array($className, $this->allowedStaticCallClasses, true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
