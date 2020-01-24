<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Order\MethodOrderByTypeFixer\Fixture;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Inspired by https://github.com/FriendsOfPHP/PHP-CS-Fixer/blob/2.8/src/Fixer/Phpdoc/NoEmptyPhpdocFixer.php
 * With difference: it doesn't add extra spaces instead of docblock.
 */
final class FixerWithAbstractParent extends AbstractFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('There should not be empty PHPDoc blocks.', [new CodeSample('<?php

/**  */
')]);
    }

    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
    }
}
