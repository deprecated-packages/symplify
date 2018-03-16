<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Guard\TokenTypeGuard;

final class ClassWrapperFactory
{
    /**
     * @var PropertyWrapperFactory
     */
    private $propertyWrapperFactory;
    /**
     * @var DocBlockWrapperFactory
     */
    private $docBlockWrapperFactory;
    /**
     * @var MethodWrapperFactory
     */
    private $methodWrapperFactory;

    public function __construct(
        DocBlockWrapperFactory $docBlockWrapperFactory,
        PropertyWrapperFactory $propertyWrapperFactory,
        MethodWrapperFactory $methodWrapperFactory
    ) {
        $this->propertyWrapperFactory = $propertyWrapperFactory;
        $this->docBlockWrapperFactory = $docBlockWrapperFactory;
        $this->methodWrapperFactory = $methodWrapperFactory;
    }

    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): ClassWrapper
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$startIndex], [T_CLASS, T_INTERFACE, T_TRAIT], self::class);

        return new ClassWrapper($tokens, $startIndex, $this->propertyWrapperFactory, $this->methodWrapperFactory);
    }
}
