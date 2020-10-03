<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\AnnotateRegexClassConstWithRegexLinkRule\AnnotateRegexClassConstWithRegexLinkRuleTest
 */
final class AnnotateRegexClassConstWithRegexLinkRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Add regex101.org link to that shows the regex in practise, so it will be easier to maintain in case of bug/extension in the future';

    /**
     * @var string
     * @see https://www.php.net/manual/en/reference.pcre.pattern.modifiers.php
     */
    private const ALL_MODIFIERS = 'imsxeADSUXJu';

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassConst::class];
    }

    /**
     * @param ClassConst $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (count((array) $node->consts) !== 1) {
            return [];
        }

        $onlyConst = $node->consts[0];
        if (! $onlyConst->value instanceof String_) {
            return [];
        }

        $constantName = (string) $onlyConst->name;
        if (! $this->isRegexPatternConstantName($constantName)) {
            return [];
        }

        $stringValue = $onlyConst->value->value;
        if (! $this->isNonSingleCharRegexPattern($stringValue)) {
            return [];
        }

        // is regex patern
        if ($this->hasDocBlockWithRegexLink($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isNonSingleCharRegexPattern(string $value): bool
    {
        // skip 1-char regexs
        if (Strings::length($value) < 4) {
            return false;
        }

        $firstChar = $value[0];

        if (ctype_alpha($firstChar)) {
            return false;
        }

        $patternWithoutModifiers = rtrim($value, self::ALL_MODIFIERS);

        if (Strings::length($patternWithoutModifiers) < 1) {
            return false;
        }

        $lastChar = Strings::substring($patternWithoutModifiers, -1, 1);
        if ($firstChar !== $lastChar) {
            return false;
        }

        // this is probably a regex
        return true;
    }

    private function hasDocBlockWithRegexLink(Node $node): bool
    {
        if ($node->getDocComment() === null) {
            return false;
        }

        $docCommentText = $node->getDocComment()
            ->getText();

        return Strings::contains($docCommentText, '@see https://regex101.com/r');
    }

    private function isRegexPatternConstantName(string $constantName): bool
    {
        return (bool) Strings::endsWith($constantName, '_REGEX');
    }
}
