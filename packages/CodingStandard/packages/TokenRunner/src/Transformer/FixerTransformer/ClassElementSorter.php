<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\Tokens;

final class ClassElementSorter
{
    /**
     * @param mixed[] $elements
     * @param mixed[] $sortedElements
     */
    public function apply(Tokens $tokens, array $elements, array $sortedElements): void
    {
        if ($sortedElements === $elements) {
            return;
        }

        $elements = array_values($elements);

        $startIndex = $elements[0]['start'] - 1;
        $endIndex = $elements[count($elements) - 1]['end'];

        $this->sortTokens($tokens, $startIndex, $endIndex, $sortedElements);
    }

    /**
     * @copied from \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::sortTokens()
     *
     * @param mixed[] $elements
     */
    private function sortTokens(Tokens $tokens, int $startIndex, int $endIndex, array $elements): void
    {
        $replaceTokens = [];

        foreach ($elements as $element) {
            for ($i = $element['start']; $i <= $element['end']; ++$i) {
                $replaceTokens[] = clone $tokens[$i];
            }
        }

        $tokens->overrideRange($startIndex + 1, $endIndex, $replaceTokens);
    }
}
