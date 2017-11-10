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

        $content = trim($varAnnotation->getContent());
        $content = rtrim($content, ' */');

        [, $types] = explode('@var', $content);

        $types = explode('|', trim($types));

        foreach ($types as $type) {
            if (! self::isIterableType($type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string[] $annotations
     */
    public static function hasAnnotations(DocBlock $docBlock, array $annotations): bool
    {
        $foundTypes = 0;
        foreach ($annotations as $annotation) {
            if ($docBlock->getAnnotationsOfType($annotation)) {
                ++$foundTypes;
            }
        }

        return $foundTypes === count($annotations);
    }

    private static function isIterableType(string $type): bool
    {
        if (Strings::endsWith($type, '[]')) {
            return true;
        }

        if ($type === 'array') {
            return true;
        }

        return false;
    }
}
