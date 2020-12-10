<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use Symplify\PackageBuilder\Matcher\ArrayStringAndFnMatcher;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ExclusiveDependencyRule\ExclusiveDependencyRuleTest
 */
final class ExclusiveDependencyRule extends AbstractSymplifyRule implements ConfigurableRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Only %s type can require %s type';

    /**
     * @var ArrayStringAndFnMatcher
     */
    private $arrayStringAndFnMatcher;

    /**
     * @var array<string, string[]>
     */
    private $allowedTypeByLocations = [];

    /**
     * @param array<string, string[]> $allowedTypeByLocations
     */
    public function __construct(ArrayStringAndFnMatcher $arrayStringAndFnMatcher, array $allowedTypeByLocations = [])
    {
        $this->arrayStringAndFnMatcher = $arrayStringAndFnMatcher;
        $this->allowedTypeByLocations = $allowedTypeByLocations;
    }

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
        $methodName = (string) $node->name;
        if ($methodName !== '__construct') {
            return [];
        }

        $className = $this->getClassName($scope);
        if ($className === null) {
            return [];
        }

        // Use loop on purpose to fetch $location variable
        // to be re-used in next params check
        $foundInLocation = false;
        $location = null;
        foreach (array_keys($this->allowedTypeByLocations) as $location) {
            if ($this->arrayStringAndFnMatcher->isMatch($className, [$location])) {
                $foundInLocation = true;
                break;
            }
        }

        return $this->processDependencyCheck($node, $foundInLocation, $location);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        $description = sprintf(self::ERROR_MESSAGE, '"Type"', '"Dependency Type"');

        return new RuleDefinition($description, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Doctrine\ORM\EntityManager;

class SomeController
{
    public function __construct(EntityManager $entityManager)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Doctrine\ORM\EntityManager;

class SomeRepository
{
    public function __construct(EntityManager $entityManager)
    {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    private function processDependencyCheck(
        ClassMethod $classMethod,
        bool $foundInLocation,
        ?string $location = null
    ): array {
        $params = $classMethod->getParams();
        $allowedTypes = $this->getAllowedTypes();
        foreach ($params as $param) {
            $type = $param->type;
            if (! $type instanceof FullyQualified) {
                continue;
            }

            $type = (string) $type;
            if (! in_array($type, $allowedTypes, true)) {
                continue;
            }

            if ($foundInLocation && in_array($type, $this->allowedTypeByLocations[$location], true)) {
                continue;
            }

            return [sprintf(self::ERROR_MESSAGE, $location, $type)];
        }

        return [];
    }

    /**
     * @return string[]
     */
    private function getAllowedTypes(): array
    {
        $allowedTypes = [];
        foreach ($this->allowedTypeByLocations as $types) {
            foreach ($types as $type) {
                $allowedTypes[] = $type;
            }
        }

        return $allowedTypes;
    }
}
