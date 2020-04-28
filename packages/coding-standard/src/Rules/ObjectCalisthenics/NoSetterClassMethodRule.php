<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules\ObjectCalisthenics;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#9-do-not-use-getters-and-setters
 *
 * @see \Symplify\CodingStandard\Tests\Rules\ObjectCalisthenics\NoSetterClassMethodRule\NoSetterClassMethodRuleTest
 */
final class NoSetterClassMethodRule implements Rule
{
    /**
     * @var string
     */
    private const SETTER_REGEX = '#^set[A-Z0-9]#';

    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Setter "%s()" is not allowed. Use constructor injection or behavior name instead, e.g. "changeName()"';

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $methodName = (string) $node->name;

        if (! Strings::match($methodName, self::SETTER_REGEX)) {
            return [];
        }

        return [sprintf(self::ERROR_MESSAGE, $methodName)];
    }
}
