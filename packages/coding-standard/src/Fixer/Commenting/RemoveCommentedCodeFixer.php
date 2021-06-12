<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\DocBlock\Decommenter;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\Php\PhpContentAnalyzer;
use Symplify\CodingStandard\Tokens\CommentedContentResolver;
use Symplify\CodingStandard\ValueObject\StartAndEnd;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://softwareengineering.stackexchange.com/a/394288/148956
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\RemoveCommentedCodeFixerTest
 */
final class RemoveCommentedCodeFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove commented code like "// $one = 1000;"';

    public function __construct(
        private CommentedContentResolver $commentedContentResolver,
        private PhpContentAnalyzer $phpContentAnalyzer,
        private Decommenter $decommenter
    ) {
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $contentWithPositions = [];

        for ($i = 0; $i < $tokens->count(); ++$i) {
            $token = $tokens[$i];
            if (! $token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if (! \str_starts_with($token->getContent(), '//')) {
                continue;
            }

            $startAndEnd = $this->commentedContentResolver->resolve($tokens, $i);

            // new position to jump to
            $i = $startAndEnd->getEnd() + 1;

            $commentedContent = $tokens->generatePartialCode($startAndEnd->getStart(), $startAndEnd->getEnd());
            $possiblePhpContent = $this->decommenter->decoment($commentedContent);

            if (! $this->phpContentAnalyzer->isPhpContent($possiblePhpContent)) {
                continue;
            }

            // clear PHP commented content
            $contentWithPositions[] = $startAndEnd;
        }

        /** @var StartAndEnd[] $contentWithPositions */
        $contentWithPositions = array_reverse($contentWithPositions);

        foreach ($contentWithPositions as $startAndEnd) {
            $realStart = $this->resolveRealStart($startAndEnd, $tokens);
            $tokens->clearRange($realStart, $startAndEnd->getEnd());
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
// $one = 1;
// $two = 2;
// $three = 3;
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * Remove the indent space ahead of comments
     *
     * @param Tokens<Token> $tokens
     */
    private function resolveRealStart(StartAndEnd $startAndEnd, Tokens $tokens): int
    {
        $preStartPosition = $startAndEnd->getStart() - 1;

        /** @var Token $preStartToken */
        $preStartToken = $tokens[$preStartPosition];

        $realStart = $startAndEnd->getStart();
        if ($preStartToken->getContent() === PHP_EOL) {
            return $realStart - 1;
        }

        if (\str_ends_with($preStartToken->getContent(), '    ')) {
            return $realStart - 1;
        }

        return $realStart;
    }
}
