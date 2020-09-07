<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules;
use PHPStan\Rules\Rule;

/**
 * @see https://williamdurand.fr/2013/06/03/object-calisthenics/#1-only-one-level-of-indentation-per-method
 *
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\SingleIndentationInMethodRule\SingleIndentationInMethodRuleTest
 */
final class SingleIndentationInMethodRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not indent more than once in class methods';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        dump($node);
        die;

        return [self::ERROR_MESSAGE];
    }
}
