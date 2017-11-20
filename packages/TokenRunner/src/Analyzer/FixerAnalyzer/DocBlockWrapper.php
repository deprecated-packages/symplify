<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\FixerAnalyzer;

use Nette\Utils\Strings;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Tokenizer\Token;

final class DocBlockWrapper
{
    /**
     * @var Token
     */
    private $token;

    public function createFromToken(Token $token): self
    {
        return new self($token);
    }

    private function __construct(Token $token)
    {
        $this->token = $token;
    }

    // @todo: turn into wrapper

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
