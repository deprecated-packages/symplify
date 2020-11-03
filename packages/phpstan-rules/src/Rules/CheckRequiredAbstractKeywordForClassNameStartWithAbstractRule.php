<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule\CheckRequiredAbstractKeywordForClassNameStartWithAbstractRuleTest
 */
final class CheckRequiredAbstractKeywordForClassNameStartWithAbstractRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Class name start with Abstract must have abstract keyword';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    /**
     * @param Class_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Identifier $shortClassName */
        $shortClassName = $node->name;
        $className = ucfirst($shortClassName->toString());

        if ($node->isAbstract() || ! Strings::startsWith($className, 'Abstract')) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
