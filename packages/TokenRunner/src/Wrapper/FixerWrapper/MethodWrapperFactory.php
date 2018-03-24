<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

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

    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(
        DocBlockWrapperFactory $docBlockWrapperFactory,
        DocBlockFinder $docBlockFinder,
        ArgumentWrapperFactory $argumentWrapperFactory,
        TokenTypeGuard $tokenTypeGuard
    ) {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->docBlockFinder = $docBlockFinder;
        $this->argumentWrapperFactory = $argumentWrapperFactory;
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): MethodWrapper
    {
        $this->tokenTypeGuard->ensureIsTokenType($tokens[$position], [T_FUNCTION], __METHOD__);

        $docBlockWrapper = null;
        $docBlockPosition = $this->docBlockFinder->findPreviousPosition($tokens, $position);

        if ($docBlockPosition) {
            $docBlockWrapper = $this->docBlockWrapperFactory->create(
                $tokens,
                $docBlockPosition,
                $tokens[$docBlockPosition]->getContent()
            );
        }

        return new MethodWrapper(
            $tokens,
            $position,
            $docBlockWrapper,
            $this->argumentWrapperFactory->createArgumentsFromTokensAndFunctionPosition($tokens, $position)
        );
    }
}
