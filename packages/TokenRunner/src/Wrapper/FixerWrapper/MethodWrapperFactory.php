<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;

final class MethodWrapperFactory
{
    /**
     * @var DocBlockWrapperFactory
     */
    private $docBlockWrapperFactory;

    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    /**
     * @var ArgumentWrapperFactory
     */
    private $argumentWrapperFactory;

    public function __construct(DocBlockWrapperFactory $docBlockWrapperFactory, DocBlockFinder $docBlockFinder, ArgumentWrapperFactory $argumentWrapperFactory)
    {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->docBlockFinder = $docBlockFinder;
        $this->argumentWrapperFactory = $argumentWrapperFactory;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): MethodWrapper
    {
        $docBlockWrapper = null;
        $docBlockPosition = $this->docBlockFinder->findPreviousPosition($tokens, $position);

        if ($docBlockPosition) {
            $docBlockWrapper = $this->docBlockWrapperFactory->create(
                $tokens,
                $docBlockPosition,
                $tokens[$docBlockPosition]->getContent()
            );
        }

        $argumentWrappers = $this->getArgumentsFromTokensAndStartPosition($tokens, $position);

        return new MethodWrapper($tokens, $position, $docBlockWrapper, $argumentWrappers);
    }

    /**
     * @return ArgumentWrapper[]
     */
    public function getArgumentsFromTokensAndStartPosition(Tokens $tokens, int $startPosition): array
    {
        $argumentsBracketStart = $tokens->getNextTokenOfKind($startPosition, ['(']);
        $argumentsBracketEnd = $tokens->findBlockEnd(
            Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
            $argumentsBracketStart
        );

        if ($argumentsBracketStart === ($argumentsBracketEnd + 1)) {
            return [];
        }

        $arguments = [];
        for ($i = $argumentsBracketStart + 1; $i < $argumentsBracketEnd; ++$i) {
            $token = $tokens[$i];

            if ($token->isGivenKind(T_VARIABLE) === false) {
                continue;
            }

            $arguments[] = $this->argumentWrapperFactory->createFromTokensAndPosition($tokens, $i);
        }

        return $arguments;
    }
}
