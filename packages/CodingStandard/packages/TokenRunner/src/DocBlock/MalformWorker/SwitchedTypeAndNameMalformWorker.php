<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

final class SwitchedTypeAndNameMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     */
    private const NAME_THEN_TYPE_PATTERN = '#@(param|var)(\s+)(?<name>\$\w+)(\s+)(?<type>[\\\\\w\[\]]+)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);

        foreach ($docBlock->getLines() as $line) {
            // $value is first, instead of type is first
            $match = Strings::match($line->getContent(), self::NAME_THEN_TYPE_PATTERN);

            if ($match === null) {
                continue;
            }

            if ($match['name'] === '' || $match['type'] === '') {
                continue;
            }

            // skip random words that look like type without autolaoding
            if (in_array($match['type'], ['The', 'Set'], true)) {
                continue;
            }

            $newLine = Strings::replace($line->getContent(), self::NAME_THEN_TYPE_PATTERN, '@$1$2$5$4$3');
            $line->setContent($newLine);
        }

        return $docBlock->getContent();
    }
}
