<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use Nette\Utils\Strings;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenNewOutsideFactoryServiceRule\ForbiddenNewOutsideFactoryServiceRuleTest
 */
final class ForbiddenNewOutsideFactoryServiceRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = '"new" outside factory is not allowed for object type %s.';

    /**
     * @var array<string, string>
     */
    private $types = [];

    /**
     * @param array<string, string> $types
     */
    public function __construct(array $types = [])
    {
        $this->types = $types;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param New_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var Class_|null $class */
        $class = $this->resolveCurrentClass($node);
        if ($class === null) {
            return [];
        }

        /** @var Identifier $classIdentifier */
        $classIdentifier = $class->namespacedName;
        $shortClassName = $classIdentifier->toString();
        if (Strings::endsWith($shortClassName, 'Factory')) {
            return [];
        }

        foreach ($this->types as $type) {
            if ($this->isHaveNewWithTypeInside($node, $type)) {
                return [sprintf(self::ERROR_MESSAGE, $type)];
            }
        }

        return [];
    }

    private function isHaveNewWithTypeInside(New_ $new, string $type): bool
    {
        /** @var FullyQualified $fullyQualifiedName */
        $fullyQualifiedName = $new->class;
        if (! $fullyQualifiedName instanceof FullyQualified) {
            return false;
        }

        $className = (string) end($fullyQualifiedName->parts);
        if (! Strings::startsWith($type, '*')) {
            return $className === $type;
        }

        return Strings::match($className, '#.' . $type . '#') > 0;
    }
}
