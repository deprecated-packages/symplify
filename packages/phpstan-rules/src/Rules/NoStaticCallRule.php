<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\ValueObject\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoStaticCallRule\NoStaticCallRuleTest
 */
final class NoStaticCallRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not use static calls';

    /**
     * @var string[]
     * @noRector Rector\Php55\Rector\String_\StringClassNameToClassConstantRector
     */
    private const DEFAULT_ALLOWED_STATIC_CALL_CLASSES = [
        // nette
        'Nette\Utils\Strings',
        'Nette\Utils\DateTime',
        'Nette\Utils\Finder',
        'Nette\Utils\FileSystem',
        'Nette\Utils\ObjectHelpers',
        'Nette\Utils\Json',
        'Nette\Utils\Arrays',
        'Nette\Utils\Reflection',
        'Ramsey\Uuid\Uuid',
        // symfony
        'Symfony\Component\Finder\Finder',
        'Symfony\Component\Yaml\Yaml',
        'Symfony\Component\Process\Process',
        'Symfony\Component\Console\Formatter\OutputFormatter',
        // symplify
        'Symplify\EasyTesting\DataProvider\StaticFixtureFinder',
        'Symplify\EasyTesting\StaticFixtureSplitter',
        'Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment',
        'Symplify\PackageBuilder\Console\Command\CommandNaming',
        // composer
        'Composer\Factory',
        // various
        'PhpCsFixer\Tokenizer\Tokens',
        'Jean85\PrettyVersions',
        'DG\BypassFinals',
        'Nette\Utils\Random',
    ];

    /**
     * @var string[]
     */
    private $allowedStaticCallClasses = [];

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @param string[] $allowedStaticCallClasses
     */
    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher, array $allowedStaticCallClasses = [])
    {
        $this->allowedStaticCallClasses = array_merge(
            $allowedStaticCallClasses,
            self::DEFAULT_ALLOWED_STATIC_CALL_CLASSES
        );

        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [StaticCall::class];
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($node->class instanceof Expr) {
            return [];
        }

        $className = (string) $node->class;
        if (in_array($className, ['self', 'parent', 'static'], true)) {
            return [];
        }

        // weird → skip
        if ($node->name instanceof Expr) {
            return [];
        }

        // skip static factories
        $method = (string) $node->name;
        if (Strings::startsWith($method, 'create')) {
            return [];
        }

        // skip static class in name
        $shortClassName = (string) Strings::after($className, '\\', -1);
        if (Strings::contains($shortClassName, 'Static')) {
            return [];
        }

        if ($this->arrayStringAndFnMatcher->isMatch($className, $this->allowedStaticCallClasses)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        return AnotherClass::staticMethod();
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $anotherClass = new AnotherClass();
        return $anotherClass->staticMethod();
    }
}
CODE_SAMPLE
            ),
        ]);
    }
}
