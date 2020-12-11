<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use ReflectionClass;

final class ClassAnnotationResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/x0Qo4x/1
     */
    private const SHORT_ANNOTATION_CLASS_REGEX = '#\@(?<short_name>[A-Z]\w+)#';

    /**
     * @return string[]
     */
    public function resolveClassAnnotations(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if ($docComment === null) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $matches = Strings::matchAll($docComment->getText(), self::SHORT_ANNOTATION_CLASS_REGEX);
        return $this->resolveShortNamesToFullyQualified($matches, $classReflection->getName());
    }

    /**
     * @param mixed[] $matches
     * @return string[]
     */
    private function resolveShortNamesToFullyQualified(array $matches, string $className): array
    {
        $reflectionClass = new ReflectionClass($className);

        $fullyQualifiedAnnotationNames = [];
        foreach ($matches as $match) {
            $fullyQualifiedAnnotationNames[] = Reflection::expandClassName($match['short_name'], $reflectionClass);
        }

        return $fullyQualifiedAnnotationNames;
    }
}
