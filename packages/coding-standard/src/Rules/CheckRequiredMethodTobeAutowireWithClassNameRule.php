<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\CheckRequiredMethodTobeAutowireWithClassNameRule\CheckRequiredMethodTobeAutowireWithClassNameRuleTest
 */
final class CheckRequiredMethodTobeAutowireWithClassNameRule extends AbstractSymplifyRule
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

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }

        if (! Strings::match($docComment->getText(), self::REQUIRED_DOCBLOCK_REGEX)) {
            return [];
        }

        $shortClassName = $this->getShortClassName($scope);
        if ($shortClassName === null) {
            return [];
        }

        if ((string) $node->name === 'autowire' . $shortClassName) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }
}
