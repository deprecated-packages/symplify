<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Rules\FunctionCallParametersCheck;
use PHPStan\Rules\Methods\CallMethodsRule;
use PHPStan\Rules\Registry;
use PHPStan\Rules\Rule;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Symplify\PHPStanRules\Rules\ForbiddenFuncCallRule;
use Symplify\PHPStanRules\Rules\NoDynamicNameRule;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;

final class TemplateRulesRegistry extends Registry
{
    /**
     * @var array<class-string<DocumentedRuleInterface>>
     */
    private const EXCLUDED_RULES = [ForbiddenFuncCallRule::class, NoDynamicNameRule::class];

    /**
     * @param array<Rule<Node>> $rules
     */
    public function __construct(array $rules)
    {
        $activeRules = $this->filterActiveRules($rules);
        parent::__construct($activeRules);
    }

    /**
     * @template TNode as \PhpParser\Node
     * @param class-string<TNode> $nodeType
     * @return array<Rule<TNode>>
     */
    public function getRules(string $nodeType): array
    {
        $activeRules = parent::getRules($nodeType);

        // only fix in a weird test case setup
        if (defined('PHPUNIT_COMPOSER_INSTALL') && $nodeType === MethodCall::class) {
            $privatesAccessor = new PrivatesAccessor();

            foreach ($activeRules as $activeRule) {
                if (! $activeRule instanceof CallMethodsRule) {
                    continue;
                }

                /** @var CallMethodsRule $activeRule */
                /** @var FunctionCallParametersCheck $check */
                $check = $privatesAccessor->getPrivateProperty($activeRule, 'check');
                $privatesAccessor->setPrivateProperty($check, 'checkArgumentTypes', true);
            }
        }

        return $activeRules;
    }

    /**
     * @param array<Rule<Node>> $rules
     * @return array<Rule<Node>>
     */
    private function filterActiveRules(array $rules): array
    {
        $activeRules = [];

        foreach ($rules as $rule) {
            foreach (self::EXCLUDED_RULES as $excludedRule) {
                if (is_a($rule, $excludedRule, true)) {
                    continue 2;
                }
            }

            $activeRules[] = $rule;
        }

        return $activeRules;
    }
}
