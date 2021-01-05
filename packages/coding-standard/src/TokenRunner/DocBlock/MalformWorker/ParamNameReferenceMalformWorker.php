<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Tokens;

final class ParamNameReferenceMalformWorker extends AbstractMalformWorker
{
    /**
     * @var string
     * @see https://regex101.com/r/B4rWNk/2
     */
    private const PARAM_NAME_REGEX = '#(?<param>@param(.*?))&(?<paramName>\$\w+)#';

    public function work(string $docContent, Tokens $tokens, int $position): string
    {
        return Strings::replace($docContent, self::PARAM_NAME_REGEX, function ($match) {
            return $match['param'] . $match['paramName'];
        });
    }
}
