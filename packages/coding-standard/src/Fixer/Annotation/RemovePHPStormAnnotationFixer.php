<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Annotation;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\RemovePHPStormAnnotationFixer\RemovePHPStormAnnotationFixerTest
 */
final class RemovePHPStormAnnotationFixer extends AbstractSymplifyFixer
{
    /**
     * @see https://regex101.com/r/nGZBzj/2
     * @var string
     */
    private const CRETED_BY_PHPSTORM_DOC_REGEX = '#\/\*\*\s+\*\s+Created by PHPStorm(.*?)\*\/#msi';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Remove "Created by PhpStorm" annotations', []);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound([T_DOC_COMMENT, T_COMMENT]);
    }

    public function fix(SplFileInfo $file, Tokens $tokens): void
    {
        $reverseTokens = $this->reverseTokens($tokens);
        foreach ($reverseTokens as $index => $token) {
            if (! $token->isGivenKind([T_DOC_COMMENT, T_COMMENT])) {
                continue;
            }

            $originalDocContent = $token->getContent();
            $cleanedDocContent = Strings::replace($originalDocContent, self::CRETED_BY_PHPSTORM_DOC_REGEX, '');
            if ($cleanedDocContent !== '') {
                continue;
            }

            // remove token
            $tokens->clearAt($index);
        }
    }
}
