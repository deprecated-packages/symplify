<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

final class MissingParamNameMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const PARAM_WITHOUT_NAME_PATTERN = '#@param ([^$]*?)( ([^$]*?))?\n#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $argumentNames = $this->getDocRelatedArgumentNames($tokens, $position);
        if ($argumentNames === null) {
            return $docContent;
        }

        $missingArgumentNames = $this->filterOutExistingParamNames($docContent, $argumentNames);
        if ($missingArgumentNames === []) {
            return $docContent;
        }

        $docBlock = new DocBlock($docContent);

        $this->completeMissingArgumentNames($missingArgumentNames, $argumentNames, $docBlock);

        return $docBlock->getContent();
    }

    /**
     * @param string[] $functionArgumentNames
     * @return string[]
     */
    private function filterOutExistingParamNames(string $docContent, array $functionArgumentNames): array
    {
        foreach ($functionArgumentNames as $key => $functionArgumentName) {
            $pattern = '# ' . preg_quote($functionArgumentName, '#') . '\b#';
            if (Strings::match($docContent, $pattern)) {
                unset($functionArgumentNames[$key]);
            }
        }

        return array_values($functionArgumentNames);
    }

    /**
     * @param string[] $missingArgumentNames
     * @param string[] $argumentNames
     */
    private function completeMissingArgumentNames(
        array $missingArgumentNames,
        array $argumentNames,
        DocBlock $docBlock
    ): void {
        foreach ($missingArgumentNames as $key => $missingArgumentName) {
            $newArgumentName = $this->resolveNewArgumentName($argumentNames, $missingArgumentName, $key);

            foreach ($docBlock->getLines() as $line) {
                if ($this->shouldSkipLine($line)) {
                    continue;
                }

                $newLineContent = $this->createNewLineContent($newArgumentName, $line);
                $line->setContent($newLineContent);
                continue 2;
            }
        }
    }

    /**
     * @param string[] $argumentNames
     */
    private function resolveNewArgumentName(array $argumentNames, string $missingArgumentName, int $key): string
    {
        if (array_search($missingArgumentName, $argumentNames, true)) {
            return $missingArgumentName;
        }

        return $argumentNames[$key];
    }

    private function shouldSkipLine(Line $line): bool
    {
        if (! Strings::contains($line->getContent(), '@param ')) {
            return true;
        }

        // already has a param name
        if (Strings::match($line->getContent(), '#@param(.*?)\$[\w]+(.*?)\n#')) {
            return true;
        }

        $match = Strings::match($line->getContent(), self::PARAM_WITHOUT_NAME_PATTERN);
        return $match === null;
    }

    private function createNewLineContent(string $newArgumentName, Line $line): string
    {
        // @see https://regex101.com/r/4FL49H/1
        $missingDollarSignPattern = '#(@param\s+([\w\|\[\]\\\\]+\s)?)(' . ltrim($newArgumentName, '$') . ')#';

        // missing \$ case - possibly own worker
        if (Strings::match($line->getContent(), $missingDollarSignPattern)) {
            return Strings::replace($line->getContent(), $missingDollarSignPattern, '$1$$3');
        }

        $replacement = '@param $1 ' . $newArgumentName . '$2' . StaticEolConfiguration::getEolChar();

        return Strings::replace($line->getContent(), self::PARAM_WITHOUT_NAME_PATTERN, $replacement);
    }
}
