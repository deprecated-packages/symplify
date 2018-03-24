<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Naming\Name\NameFactory;

final class PropertyWrapperFactory
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
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    public function __construct(
        DocBlockWrapperFactory $docBlockWrapperFactory,
        DocBlockFinder $docBlockFinder,
        NameFactory $nameFactory,
        TokenTypeGuard $tokenTypeGuard
    ) {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->docBlockFinder = $docBlockFinder;
        $this->nameFactory = $nameFactory;
        $this->tokenTypeGuard = $tokenTypeGuard;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): PropertyWrapper
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

        $this->tokenTypeGuard->ensureIsTokenType($tokens[$position], [T_VARIABLE], __METHOD__);

        return new PropertyWrapper($tokens, $position, $docBlockWrapper, $this->nameFactory);
    }
}
