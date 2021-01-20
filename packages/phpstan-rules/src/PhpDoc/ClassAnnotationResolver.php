<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ReflectionProvider;

final class ClassAnnotationResolver
{
    /**
     * @var string
     */
    private const SHORT_NAME_PART = 'short_name';

    /**
     * @var string
     * @see https://regex101.com/r/x0Qo4x/1
     */
    private const SHORT_ANNOTATION_CLASS_REGEX = '#\@(?<' . self::SHORT_NAME_PART . '>[A-Z]\w+)#';

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    /**
     * @return string[]
     */
    public function resolveClassAnnotations(Node $node, Scope $scope): array
    {
        $docComment = $node->getDocComment();
        if (! $docComment instanceof Doc) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
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
        if (! class_exists($className)) {
            return [];
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $fullyQualifiedAnnotationNames = [];
        foreach ($matches as $match) {
            $fullyQualifiedAnnotationNames[] = Reflection::expandClassName(
                $match[self::SHORT_NAME_PART],
                $classReflection->getNativeReflection()
            );
        }

        return $fullyQualifiedAnnotationNames;
    }
}
