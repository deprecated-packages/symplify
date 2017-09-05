<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tokenizer;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;

final class DocBlockAnalyzer
{
    public static function isArrayProperty(Token $token): bool
    {
        $docBlock = new DocBlock($token->getContent());

        if (! $docBlock->getAnnotationsOfType('var')) {
            return false;
        }

        $varAnnotation = $docBlock->getAnnotationsOfType('var')[0];
        $varTypes = $varAnnotation->getTypes();
        if (! count($varTypes)) {
            return false;
        }

        return Strings::contains($varTypes[0], '[]');
    }
}
