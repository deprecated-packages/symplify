<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObjectFactory;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineLengthAndPosition;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

final class LineLengthAndPositionFactory
{
    public function createFromTokensAndLineStartPosition(Tokens $tokens, int $currentPosition): LineLengthAndPosition
    {
        $length = 0;

        while (! $this->isNewLineOrOpenTag($tokens, $currentPosition)) {
            // in case of multiline string, we are interested in length of the part on current line only
            if (! isset($tokens[$currentPosition])) {
                throw new TokenNotFoundException($currentPosition);
            }

            $explode = explode("\n", $tokens[$currentPosition]->getContent());
            // string precedes current token, so we are interested in end part only
            if (count($explode) !== 0) {
                $lastSection = end($explode);
                $length += strlen($lastSection);
            }

            --$currentPosition;

            if (count($explode) > 1) {
                // no longer need to continue searching for newline
                break;
            }

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return new LineLengthAndPosition($length, $currentPosition);
    }

    /**
     * @param Tokens|Token[] $tokens
     */
    private function isNewLineOrOpenTag(Tokens $tokens, int $position): bool
    {
        if (! isset($tokens[$position])) {
            throw new TokenNotFoundException($position);
        }

        if (Strings::startsWith($tokens[$position]->getContent(), StaticEolConfiguration::getEolChar())) {
            return true;
        }

        return $tokens[$position]->isGivenKind(T_OPEN_TAG);
    }
}
