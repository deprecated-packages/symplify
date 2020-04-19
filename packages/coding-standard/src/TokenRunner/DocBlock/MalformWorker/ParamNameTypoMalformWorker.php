<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;

final class ParamNameTypoMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     */
    private const PARAM_NAME_PATTERN = '#@param(.*?)(?<paramName>\$\w+)#';

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
        $paramAnnotations = $this->getAnnotationsOfType($docContent, 'param');

        $paramNames = [];
        foreach ($paramAnnotations as $paramAnnotation) {
            $match = Strings::match($paramAnnotation->getContent(), self::PARAM_NAME_PATTERN);
            if (isset($match['paramName'])) {
                $paramNames[] = $match['paramName'];
            }
        }

        return $paramNames;
    }

    /**
     * @return Annotation[]
     */
    private function getAnnotationsOfType(string $docContent, string $type): array
    {
        $docBlock = new DocBlock($docContent);

        return $docBlock->getAnnotationsOfType($type);
    }
}
