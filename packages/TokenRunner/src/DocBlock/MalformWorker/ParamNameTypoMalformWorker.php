<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;

final class ParamNameTypoMalformWorker extends AbstractMalformWorker
{
    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        $argumentNames = $this->getDocRelatedArgumentNames($tokens, $position);
        if ($argumentNames === null) {
            return $docContent;
        }

        $paramNames = $this->getParamNames($docContent);

        // remove correct params
        foreach ($argumentNames as $key => $argumentName) {
            if (in_array($argumentName, $paramNames, true)) {
                unset($paramNames[array_search($argumentName, $paramNames, true)]);
                unset($argumentNames[$key]);
            }
        }

        // nothing to edit, all arguments are correct or there are no more @param annotations
        if ($argumentNames === [] || $paramNames === []) {
            return $docContent;
        }

        // let's try to fix the typos
        foreach ($argumentNames as $key => $argumentName) {
            // 1. the same position
            if (isset($paramNames[$key])) {
                $typoName = $paramNames[$key];
                $replacePattern = '#@param(.*?)' . preg_quote($typoName, '#') . '#';

                $docContent = Strings::replace($docContent, $replacePattern, '@param$1' . $argumentName);
            }

            // @todo other cases
        }

        return $docContent;
    }

    /**
     * @return string[]
     */
    private function getParamNames(string $docContent): array
    {
        $docBlock = new DocBlock($docContent);

        $paramNames = [];
        foreach ($docBlock->getAnnotationsOfType('param') as $paramLine) {
            $match = Strings::match($paramLine->getContent(), '#@param(.*?)(?<paramName>\$\w+)#');
            if (isset($match['paramName'])) {
                $paramNames[] = $match['paramName'];
            }
        }

        return $paramNames;
    }
}
