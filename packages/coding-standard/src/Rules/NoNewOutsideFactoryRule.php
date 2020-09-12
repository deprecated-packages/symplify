<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Token;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoNewOutsideFactoryRule\NoNewOutsideFactoryRuleTest
 */
final class NoNewOutsideFactoryRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use decouled factory service to create "%s" object';

    /**
     * @var string[]
     */
    private const ALLOWED_CLASSES = [
        'DateTime', 'SplFileInfo', SmartFileInfo::class, Token::class,
        ReflectionClass::class,
        ReflectionFunction::class,
        ReflectionMethod::class,

        // symfony
        FileLocator::class,
        PhpFileLoader::class,
        // php cs fixes
        FixerDefinition::class,
    ];

    public function getNodeType(): string
    {
        return New_::class;
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $node->class instanceof Name) {
            return [];
        }

        $newClassName = $node->class->toString();
        if (in_array($newClassName, self::ALLOWED_CLASSES, true)) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $currentClassName = $classReflection->getName();
        if (Strings::endsWith($currentClassName, 'Factory')) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $newClassName);
        return [$errorMessage];
    }
}
