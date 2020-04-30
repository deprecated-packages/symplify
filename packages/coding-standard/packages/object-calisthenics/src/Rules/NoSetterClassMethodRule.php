<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#9-do-not-use-getters-and-setters
 *
 * @see \Symplify\CodingStandard\ObjectCalisthenics\Tests\Rules\NoSetterClassMethodRule\NoSetterClassMethodRuleTest
 */
final class NoSetterClassMethodRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Setter "%s()" is not allowed. Use constructor injection or behavior name instead, e.g. "changeName()"';

    /**
     * @var string
     */
    private const SETTER_REGEX = '#^set[A-Z0-9]#';

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
