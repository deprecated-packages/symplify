<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;

final class SuperfluousVarNameMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const VAR_VARIABLE_NAME_PATTERN = '#(@var)(?<type>\s+[|\\\\\w]+)?(\s+)(?<propertyName>\$[\w]+)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        if ($this->shouldSkip($tokens, $position)) {
            return $docContent;
        }

        $docBlock = new DocBlock($docContent);

        foreach ($docBlock->getLines() as $line) {
            $match = Strings::match($line->getContent(), self::VAR_VARIABLE_NAME_PATTERN);
            if ($match === null) {
                continue;
            }

            $newLineContent = Strings::replace(
                $line->getContent(),
                self::VAR_VARIABLE_NAME_PATTERN,
                function (array $match): string {
                    $replacement = $match[1];
                    if ($match['type'] !== []) {
                        $replacement .= $match['type'];
                    }

                    if (Strings::match($match[0], '#\$this$#')) {
                        return Strings::replace($match[0], '#\$this$#', 'self');
                    }

                    return $replacement;
                }
            );

            $line->setContent($newLineContent);
        }

        return $docBlock->getContent();
    }

    /**
     * Is property doc block?
     */
    private function shouldSkip(Tokens $tokens, int $position): bool
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($position);

        // nothing to change
        if ($nextMeaningfulTokenPosition === null) {
            return true;
        }

        $nextMeaningfulToken = $tokens[$nextMeaningfulTokenPosition];

        // should be protected/private/public/static, to know we're property
        return ! $nextMeaningfulToken->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC]);
    }
}
