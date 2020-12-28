<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\RegexpException;
use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\PHPStanRules\ValueObject\Regex;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\CheckConstantStringValueFormatRuleTest
 */
final class CheckConstantStringValueFormatRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant string value need to only have small letters, _, -, . and numbers';

    /**
     * @var string
     * @see https://regex101.com/r/92F0op/4
     */
    private const ALLOWED_FORMAT_REGEX = '#^[a-z0-9_\.-]+$#';

    /**
     * @var string[]
     */
    private const ALLOWED_CONST_NAMES = ['*_REGEX'];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        ArrayStringAndFnMatcher $arrayStringAndFnMatcher,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->simpleNameResolver = $simpleNameResolver;
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
        if ($this->shouldSkipForValueObjectNamespace($scope)) {
            return [];
        }

        $consts = (array) $node->consts;
        if ($consts === []) {
            return [];
        }

        foreach ($consts as $const) {
            $constName = $this->simpleNameResolver->getName($const->name);
            if ($constName === null) {
                continue;
            }

            if ($this->arrayStringAndFnMatcher->isMatch($constName, self::ALLOWED_CONST_NAMES)) {
                continue;
            }

            if (! $const->value instanceof String_) {
                continue;
            }

            if ($this->shouldSkipStringValue($const->value)) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    private const FOO = '$not_ok$';
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private const FOO = 'bar';
}
CODE_SAMPLE
            ),
        ]);
    }

    private function shouldSkipForValueObjectNamespace(Scope $scope): bool
    {
        $className = $this->getClassName($scope);
        if ($className === null) {
            return false;
        }

        return (bool) Strings::match($className, Regex::VALUE_OBJECT_REGEX);
    }

    private function isUrlString(String_ $string): bool
    {
        return (bool) Strings::startsWith($string->value, 'http');
    }

    private function shouldSkipStringValue(String_ $string): bool
    {

        // has a space, not a string value
        if (Strings::match($string->value, '#\s#')) {
            return true;
        }

        // should skip string value
        if ($this->isUrlString($string)) {
            return true;
        }

        // is regular expression?
        try {
            Strings::match('', $string->value);
            return true;
        } catch (RegexpException $regexpException) {
        }

        return (bool) Strings::match($string->value, self::ALLOWED_FORMAT_REGEX);
    }
}
