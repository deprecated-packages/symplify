<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Const_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\PropertyProperty;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;

/**
 * @see https://github.com/object-calisthenics/phpcs-calisthenics-rules#6-do-not-abbreviate
 *
 * @see \Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoShortNameRule\NoShortNameRuleTest
 */
final class NoShortNameRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not name "%s", shorter than %d chars';

    /**
     * @var int
     */
    private $minNameLenght;

    /**
     * @var string[]
     */
    private $allowedShortNames = [];

    /**
     * @param string[] $allowedShortNames
     */
    public function __construct(int $minNameLenght, array $allowedShortNames)
    {
        $this->minNameLenght = $minNameLenght;
        $this->allowedShortNames = $allowedShortNames;
    }

    /**
     * @return class-string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassLike::class, Function_::class, ClassMethod::class, Const_::class, PropertyProperty::class];
    }

    /**
     * @param ClassLike|Function_|ClassMethod|Const_|PropertyProperty $node
     * @return array<int, string>
     */
    public function process(Node $node, Scope $scope): array
    {
        $name = (string) $node->name;
        if (Strings::length($name) >= $this->minNameLenght) {
            return [];
        }

        if (in_array($name, $this->allowedShortNames, true)) {
            return [];
        }

        $errorMessage = sprintf(self::ERROR_MESSAGE, $name, $this->minNameLenght);
        return [$errorMessage];
    }
}
