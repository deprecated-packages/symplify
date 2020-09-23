<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassName\CheckRequiredMethodTobeAutowireWithClassNameTest
 */
final class CheckRequiredMethodTobeAutowireWithClassName implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method with @required need to be named autowire+class name';

    /**
     * @var string
     * @see https://regex101.com/r/gn2P0C/1
     */
    private const REQUIRED_DOCBLOCK_REGEX = '#\*\s+@required\n?#';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }

        if (! Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
            return [];
        }

        $class = $node->getAttribute('parent');
        /** @var Identifier $name */
        $name = $class->name;
        $className = $name->toString();

        if ((string) $node->name === 'autowire' . $className) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
