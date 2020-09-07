<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\NoStaticCallRuleTest
 */
final class NoStaticCallRule extends AbstractManyNodeTypeRule
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
    ];

    /**
     * @var string[]
     */
    private $allowedStaticCallClasses = [];

    /**
     * @param string[] $allowedStaticCallClasses
     */
    public function __construct(array $allowedStaticCallClasses = [])
    {
        $this->allowedStaticCallClasses = array_merge(
            $allowedStaticCallClasses,
            self::DEFAULT_ALLOWED_STATIC_CALL_CLASSES
        );
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

        if (in_array($className, $this->allowedStaticCallClasses, true)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
