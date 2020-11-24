<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\Blueprint;

use Symplify\PHPMDDecomposer\ValueObject\Config\MatchToPHPStanConfig;
use Symplify\PHPMDDecomposer\ValueObject\Config\PHPStanConfig;
use Symplify\PHPStanRules\ObjectCalisthenics\Rules\NoElseAndElseIfRule;
use Symplify\PHPStanRules\Rules\NoDefaultParameterValueRule;

final class PHPMDToPHPStanBluerprint
{
    /**
     * @var string
     */
    private const SYMPLIFY = 'symplify';

    /**
     * @var MatchToPHPStanConfig[]
     */
    private $matchesToPHPStanConfigs = [];

    public function __construct()
    {
        $this->createCognitiveComplexity();

        # see https://phpmd.org/rules/cleancode.html → https://github.com/symplify/coding-standard/blob/master/docs/phpstan_rules.md
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/cleancode.xml/BooleanArgumentFlag',
            new PHPStanConfig([NoDefaultParameterValueRule::class])
        );

        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/cleancode.xml/ElseExpression',
            new PHPStanConfig([NoElseAndElseIfRule::class])
        );

        // https://github.com/symplify/coding-standard/blob/master/docs/phpstan_rules.md
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/codesize.xml/ExcessiveParameterList',
            new PHPStanConfig(['Symplify\CodingStandard\Rules\ExcessiveParameterListRule'])
        );

        // https://github.com/symplify/coding-standard/blob/master/docs/phpstan_rules.md#no-names-shorter-than-3-chars
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/naming.xml/ShortVariable',
            new PHPStanConfig(['Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule'])
        );
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/naming.xml/ShortMethodName',
            new PHPStanConfig(['Symplify\CodingStandard\ObjectCalisthenics\Rules\NoShortNameRule'])
        );

        // how to covert paraemters
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig('rulesets/cleancode.xml/StaticAccess', new PHPStanConfig(
            ['Symplify\PHPStanRules\Rules\NoStaticCallRule'],
            [],
            [],
            [
                'exceptions' => [
                    self::SYMPLIFY => [
                        'allowed_static_call_classes' => '%value%',
                    ],
                ],
            ]
        ));

        // @see https://github.com/Slamdunk/phpstan-extensions
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/design.xml/GotoStatement',
            new PHPStanConfig(['SlamPhpStan\GotoRule'])
        );

        // https://github.com/symplify/coding-standard/blob/master/docs/phpstan_rules.md
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/naming.xml/BooleanGetMethodName',
            new PHPStanConfig(['Symplify\PHPStanRules\Rules\BoolishClassMethodPrefixRule'])
        );

        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/naming.xml/LongVariable',
            new PHPStanConfig(['Symplify\PHPStanRules\Rules\TooLongVariableRule'])
        );

        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/design.xml/EvalExpression',
            new PHPStanConfig(['Ergebnis\PHPStan\Rules\Expressions\NoEvalRule'])
        );

        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/naming.xml/ConstantNamingConventions',
            new PHPStanConfig(['Symplify\PHPStanRules\Rules\UppercaseConstantRule'])
        );

        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig('rulesets/codesize.xml/TooManyFields', new PHPStanConfig(
            ['Symplify\PHPStanRules\Rules\TooManyFieldsRule'],
            [],
            [],
            [
                'maxfields' => [
                    self::SYMPLIFY => [
                        'max_property_count' => '%value%',
                    ],
                ],
            ]
        ));

        // @see https://github.com/thecodingmachine/phpstan-strict-rules
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/controversial.xml/Superglobals',
            new PHPStanConfig(['TheCodingMachine\PHPStan\Rules\Superglobals\NoSuperglobalsRule'])
        );
    }

    /**
     * @return MatchToPHPStanConfig[]
     */
    public function provide(): array
    {
        return $this->matchesToPHPStanConfigs;
    }

    private function createCognitiveComplexity(): void
    {
        $phpStanConfig = new PHPStanConfig(
            [],
            [
                self::SYMPLIFY => [
                    'max_method_cognitive_complexity' => 8,
                    'max_class_cognitive_complexity' => 50,
                ],
            ],
            ['vendor/symplify/coding-standard/packages/cognitive-complexity/config/cognitive-complexity-rules.neon']
        );

        // parameters with configuration
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/codesize.xml/CyclomaticComplexity',
            $phpStanConfig
        );
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/codesize.xml/NPathComplexity',
            $phpStanConfig
        );
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/codesize.xml/ExcessiveMethodLength',
            $phpStanConfig
        );
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/codesize.xml/ExcessiveClassLength',
            $phpStanConfig
        );
        $this->matchesToPHPStanConfigs[] = new MatchToPHPStanConfig(
            'rulesets/codesize.xml/TooManyMethods',
            $phpStanConfig
        );
    }
}
