<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRuleTest
 */
final class CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class name start with Abstract must have abstract keyword';

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var Identifier $name */
        $name = $node->name;
        $className = ucfirst($name->toString());

        if ($node->isAbstract() || ! Strings::startsWith($className, 'Abstract')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
