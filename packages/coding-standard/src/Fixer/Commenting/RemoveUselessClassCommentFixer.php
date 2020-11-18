<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Fixer\Commenting;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessClassCommentFixer\RemoveUselessClassCommentFixerTest
 */
final class RemoveUselessClassCommentFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @see https://regex101.com/r/RzTdFH/4
     * @var string
     */
    private const COMMENT_CLASS_REGEX = '#(\/\*{2}\s+?)?(\*|\/\/)\s+[cC]lass\s+[^\s]*(\s+\*\/)?$#';

    /**
     * @see https://regex101.com/r/bzbxXz/2
     * @var string
     */
    private const COMMENT_CONSTRUCTOR_CLASS_REGEX = '#^\s{0,}(\/\*{2}\s+?)?(\*|\/\/)\s+[^\s]*\s+[Cc]onstructor\.?(\s+\*\/)?$#';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove useless "// Class <Some>" or "// <Some> Constructor." comment';

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
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
            $cleanedDocContent = Strings::replace($originalDocContent, self::COMMENT_CLASS_REGEX, '');
            $cleanedDocContent = Strings::replace($cleanedDocContent, self::COMMENT_CONSTRUCTOR_CLASS_REGEX, '');

            if ($cleanedDocContent !== '') {
                continue;
            }

            // remove token
            $tokens->clearAt($index);
        }
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
/**
 * class SomeClass
 */
class SomeClass
{
    /**
     * SomeClass Constructor.
     */
    public function __construct()
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct()
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
