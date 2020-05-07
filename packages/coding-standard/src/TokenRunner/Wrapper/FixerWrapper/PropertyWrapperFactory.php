<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\DocBlock\DocBlockManipulator;
use Symplify\CodingStandard\TokenRunner\Guard\TokenTypeGuard;
use Symplify\CodingStandard\TokenRunner\Naming\Name\NameFactory;
use Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\PropertyWrapper;

final class PropertyWrapperFactory
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
     * @var DocBlockManipulator
     */
    private $docBlockManipulator;

    public function __construct(
        NameFactory $nameFactory,
        TokenTypeGuard $tokenTypeGuard,
        DocBlockManipulator $docBlockManipulator
    ) {
        $this->nameFactory = $nameFactory;
        $this->tokenTypeGuard = $tokenTypeGuard;
        $this->docBlockManipulator = $docBlockManipulator;
    }

    public function createFromTokensAndPosition(Tokens $tokens, int $position): PropertyWrapper
    {
        /** @var Token $token */
        $token = $tokens[$position];

        $this->tokenTypeGuard->ensureIsTokenType($token, [T_VARIABLE], __METHOD__);

        return new PropertyWrapper($tokens, $position, $this->nameFactory, $this->docBlockManipulator);
    }
}
