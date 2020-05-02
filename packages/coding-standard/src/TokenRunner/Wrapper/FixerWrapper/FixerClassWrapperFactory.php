<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Guard\TokenTypeGuard;
use Symplify\CodingStandard\TokenRunner\Naming\Name\NameFactory;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\FixerClassWrapper;
use Symplify\PackageBuilder\Types\ClassLikeExistenceChecker;

final class FixerClassWrapperFactory
{
    /**
     * @var NameFactory
     */
    private $nameFactory;

    /**
     * @var TokenTypeGuard
     */
    private $tokenTypeGuard;

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    public function __construct(
        NameFactory $nameFactory,
        TokenTypeGuard $tokenTypeGuard,
        ClassLikeExistenceChecker $classLikeExistenceChecker
    ) {
        $this->nameFactory = $nameFactory;
        $this->tokenTypeGuard = $tokenTypeGuard;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }

    public function createFromTokensArrayStartPosition(Tokens $tokens, int $startIndex): FixerClassWrapper
    {
        /** @var Token $token */
        $token = $tokens[$startIndex];
        $this->tokenTypeGuard->ensureIsTokenType($token, [T_CLASS, T_INTERFACE, T_TRAIT], self::class);

        return new FixerClassWrapper($tokens, $startIndex, $this->nameFactory, $this->classLikeExistenceChecker);
    }
}
