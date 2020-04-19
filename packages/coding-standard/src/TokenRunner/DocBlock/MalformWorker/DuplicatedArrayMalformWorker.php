<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;

final class DuplicatedArrayMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const IMPLICIT_ARRAY_WITH_ARRAY_PATTERN_LEFT = '#((\w+\(?\)?\[\])(\|(.*?))?)\|array#';

    /**
     * @var string
     */
    private const IMPLICIT_ARRAY_WITH_ARRAY_PATTERN_RIGHT = '#array\|((.*?\|)?(\w+\(?\)?\[\]))#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);

        foreach ($docBlock->getLines() as $line) {
            $newContent = Strings::replace($line->getContent(), self::IMPLICIT_ARRAY_WITH_ARRAY_PATTERN_LEFT, '$1');
            $newContent = Strings::replace($newContent, self::IMPLICIT_ARRAY_WITH_ARRAY_PATTERN_RIGHT, '$1');

            $line->setContent($newContent);
        }

        return $docBlock->getContent();
    }
}
