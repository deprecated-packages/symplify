<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\FileNode;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule\ForbiddenMultipleClassLikeInOneFileRuleTest
 */
final class ForbiddenMultipleClassLikeInOneFileRule implements \PHPStan\Rules\Rule, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Multiple class/interface/trait is not allowed in single file';

    public function __construct(
        private NodeFinder $nodeFinder
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeType(): string
    {
        return FileNode::class;
    }

    /**
     * @param FileNode $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        /** @var ClassLike[] $classLikes */
        $classLikes = $this->nodeFinder->findInstanceOf($node->getNodes(), ClassLike::class);

        $findclassLikes = [];
        foreach ($classLikes as $classLike) {
            if ($classLike->name === null) {
                continue;
            }

            $findclassLikes[] = $classLike;
        }

        if (count($findclassLikes) <= 1) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// src/SomeClass.php
class SomeClass
{
}

interface SomeInterface
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
// src/SomeClass.php
class SomeClass
{
}

// src/SomeInterface.php
interface SomeInterface
{
}
CODE_SAMPLE
            ),
        ]);
    }
}
