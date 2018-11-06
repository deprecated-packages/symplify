<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Fixer\DefinedFixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

abstract class AbstractSymplifyFixer implements DefinedFixerInterface
{
    public function getPriority(): int
    {
        return 0;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function isRisky(): bool
    {
        return false;
    }

    public function supports(SplFileInfo $file): bool
    {
        return true;
    }

    /**
     * @return int[]
     */
    protected function getReversedClassyPositions(Tokens $tokens): array
    {
        $classyTokensByTokenKind = $tokens->findGivenKind(Token::getClassyTokenKinds());
        $classyTokens = array_replace(...$classyTokensByTokenKind);

        return array_reverse(array_keys($classyTokens));
    }
}
