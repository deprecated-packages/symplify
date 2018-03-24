<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Analyzer\FixerAnalyzer\DocBlockFinder;
use Symplify\TokenRunner\Guard\TokenTypeGuard;
use Symplify\TokenRunner\Naming\Name\NameFactory;

final class ClassWrapperFactory
{
    /**
     * @var PropertyWrapperFactory
     */
    private $propertyWrapperFactory;

    /**
     * @var MethodWrapperFactory
     */
    private $methodWrapperFactory;

    /**
     * @var DocBlockFinder
     */
    private $docBlockFinder;

    /**
     * @var PropertyAccessWrapperFactory
     */
    private $propertyAccessWrapperFactory;

    /**
     * @var NameFactory
     */
    private $nameFactory;

    public function __construct(
        PropertyWrapperFactory $propertyWrapperFactory,
        MethodWrapperFactory $methodWrapperFactory,
        DocBlockFinder $docBlockFinder,
        PropertyAccessWrapperFactory $propertyAccessWrapperFactory,
        NameFactory $nameFactory
    ) {
        $this->propertyWrapperFactory = $propertyWrapperFactory;
        $this->methodWrapperFactory = $methodWrapperFactory;
        $this->docBlockFinder = $docBlockFinder;
        $this->propertyAccessWrapperFactory = $propertyAccessWrapperFactory;
        $this->nameFactory = $nameFactory;
    }

    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): ClassWrapper
    {
        TokenTypeGuard::ensureIsTokenType($tokens[$startIndex], [T_CLASS, T_INTERFACE, T_TRAIT], self::class);

        return new ClassWrapper(
            $tokens,
            $startIndex,
            $this->propertyWrapperFactory,
            $this->methodWrapperFactory,
            $this->docBlockFinder,
            $this->propertyAccessWrapperFactory,
            $this->nameFactory
        );
    }
}
