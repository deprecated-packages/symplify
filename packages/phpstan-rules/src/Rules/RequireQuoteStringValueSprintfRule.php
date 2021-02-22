<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\RequireQuoteStringValueSprintfRule\RequireQuoteStringValueSprintfRuleTest
 */
final class RequireQuoteStringValueSprintfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"%s" in sprintf() format must be quoted';

    /**
     * @see https://regex101.com/r/qDY6y0/1
     * @var string
     */
    private const UNQUOTED_STRING_MASK_REGEX = '#[^\'"s](%s)[^\'"%]#';

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver)
    {
        $this->simpleNameResolver = $simpleNameResolver;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class];
    }

    /**
     * @param FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->simpleNameResolver->isName($node, 'sprintf')) {
            return [];
        }

        $args = $node->args;
        if (count($args) === 1) {
            return [];
        }

        $format = $args[0]->value;
        if (! $format instanceof String_) {
            return [];
        }

        if (! $this->doesContainUnquotedSMask($format->value)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        echo sprintf('%s value', $variable);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function run()
    {
        echo sprintf('"%s" value', $variable);
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function doesContainUnquotedSMask(string $content): bool
    {
        $matches = Strings::match($content, self::UNQUOTED_STRING_MASK_REGEX);
        if ($matches !== null) {
            return true;
        }

        if (Strings::startsWith($content, '%s ')) {
            return true;
        }

        return Strings::endsWith($content, ' %s');
    }
}
