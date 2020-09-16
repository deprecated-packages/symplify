<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\Contract\RegexRuleInterface;

abstract class AbstractRegexRule extends AbstractManyNodeTypeRule implements RegexRuleInterface
{
    /**
     * @var string[]
     */
    private const FUNC_CALLS_WITH_FIRST_ARG_REGEX = [
        'preg_match',
        'preg_match_all',
        'preg_split',
        'preg_replace',
        'preg_replace_callback',
    ];

    /**
     * @var string[]
     */
    private const NETTE_UTILS_CALLS_METHOD_NAMES_WITH_SECOND_ARG_REGEX = ['match', 'matchAll', 'replace', 'split'];

    /**
     * @var string
     */
    private const NETTE_UTILS_STRINGS_CLASS = Strings::class;

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class, FuncCall::class];
    }

    /**
     * @param StaticCall|FuncCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node instanceof FuncCall) {
            return $this->processFuncCall($node);
        }

        return $this->processStaticCall($node);
    }

    /**
     * @return string[]
     */
    private function processFuncCall(FuncCall $funcCall): array
    {
        if ($funcCall->name instanceof Expr) {
            return [];
        }

        $funcCallName = (string) $funcCall->name;
        if (! in_array($funcCallName, self::FUNC_CALLS_WITH_FIRST_ARG_REGEX, true)) {
            return [];
        }

        return $this->processRegexFuncCall($funcCall);
    }

    /**
     * @return string[]
     */
    private function processStaticCall(StaticCall $staticCall): array
    {
        if ($staticCall->class instanceof Expr) {
            return [];
        }

        if ($staticCall->name instanceof Expr) {
            return [];
        }

        $className = (string) $staticCall->class;

        if ($className !== self::NETTE_UTILS_STRINGS_CLASS) {
            return [];
        }

        $methodName = (string) $staticCall->name;
        if (! in_array($methodName, self::NETTE_UTILS_CALLS_METHOD_NAMES_WITH_SECOND_ARG_REGEX, true)) {
            return [];
        }

        return $this->processRegexStaticCall($staticCall);
    }
}
