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

    public function __construct(DocBlockWrapperFactory $docBlockWrapperFactory, DocBlockFinder $docBlockFinder)
    {
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->docBlockFinder = $docBlockFinder;
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

        return new MethodWrapper($tokens, $position, $docBlockWrapper);
    }
}
