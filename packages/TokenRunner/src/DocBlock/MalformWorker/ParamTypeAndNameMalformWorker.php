<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

final class ParamTypeAndNameMalformWorker implements MalformWorkerInterface
{
    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);

        foreach ($docBlock->getLines() as $line) {
            // $value is first, instead of type is first
            // @todo const
            $match = Strings::match($line->getContent(), '#@param[\s+](?<name>\$\w+)[\s+](?<type>[\\\\\w\[\]]+)#');

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

            // @todo const
            $pattern = '#@param(\s+)(\$\w+)(\s+)([\\\\\w\[\]]+)#';
            $newLine = Strings::replace($line->getContent(), $pattern, '@param$1$4$3$2');

            $line->setContent($newLine);
        }

        return $docBlock->getContent();
    }
}
