<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

final class DuplicatedArrayMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/wKRh0x/2
     */
    private const IMPLICIT_ARRAY_WITH_ARRAY_LEFT_REGEX = '#((\w+\(?\)?\[\])(\|(.*?))?)\|array#';

    /**
     * @var string
     * @see https://regex101.com/r/Op8pAz/1
     */
    private const IMPLICIT_ARRAY_WITH_ARRAY_RIGHT_REGEX = '#array\|((.*?\|)?(\w+\(?\)?\[\]))#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);

        $lines = $docBlock->getLines();
        foreach ($lines as $line) {
            $newContent = Strings::replace($line->getContent(), self::IMPLICIT_ARRAY_WITH_ARRAY_LEFT_REGEX, '$1');
            $newContent = Strings::replace($newContent, self::IMPLICIT_ARRAY_WITH_ARRAY_RIGHT_REGEX, '$1');

            $line->setContent($newContent);
        }

        return $docBlock->getContent();
    }
}
