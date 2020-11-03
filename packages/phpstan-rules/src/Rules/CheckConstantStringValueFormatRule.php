<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\CheckConstantStringValueFormatRuleTest
 */
final class CheckConstantStringValueFormatRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant string value need to only have small letters, underscore, dash, fullstop, and numbers';

    /**
     * @var string
     * @see https://regex101.com/r/92F0op/4
     */
    private const FORMAT_REGEX = '#^[a-z0-9_\.-]+$#';

    /**
     * @var string[]
     */
    private const ALLOWED_CONST_NAMES = ['ERROR_MESSAGE', '*_REGEX', 'ALLOWED_CONST_NAMES'];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher)
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }

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
        $consts = $node->consts;
        if ($consts === []) {
            return [];
        }

        foreach ($consts as $const) {
            if ($this->arrayStringAndFnMatcher->isMatch($const->name->toString(), self::ALLOWED_CONST_NAMES)) {
                continue;
            }

            if (! $const->value instanceof String_) {
                continue;
            }

            if (! Strings::match($const->value->value, self::FORMAT_REGEX)) {
                return [self::ERROR_MESSAGE];
            }
        }

        return [];
    }
}
