<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\PhpDoc;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\GenericTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\Reflection\ReflectionProvider;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor;

final class FullyQualifyingPhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @var string
     */
    private const CLASS_SNIPPET_PART = 'class_snippet';

    /**
     * @var string
     * @see https://regex101.com/r/2OYung/1
     */
    private const PARTIAL_CLASS_REFERENCE_REGEX = '#(?<' . self::CLASS_SNIPPET_PART . '>[A-Za-z_\\\\]+)::class#';

    /**
     * @var string
     */
    private $className;

    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
    }

    public function configureClassName(string $className): void
    {
        $this->className = $className;
    }

    public function enterNode(Node $node): ?Node
    {
        if ($node instanceof PhpDocTagNode) {
            $this->processPhpDocTagNode($node);
        }

        if ($node instanceof GenericTagValueNode) {
            $this->processGenericTagValueNode($node);
        }

        return $node;
    }

    private function resolveShortNamesToFullyQualified(string $currentName, string $className): ?string
    {
        if (! class_exists($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);
        return Reflection::expandClassName($currentName, $classReflection->getNativeReflection());
    }

    private function processPhpDocTagNode(PhpDocTagNode $phpDocTagNode): void
    {
        $shortClassName = trim($phpDocTagNode->name, '@');

        $resolvedFullyQualifiedName = $this->resolveShortNamesToFullyQualified($shortClassName, $this->className);
        if ($resolvedFullyQualifiedName) {
            $phpDocTagNode->name = $resolvedFullyQualifiedName;
        }
    }

    private function processGenericTagValueNode(GenericTagValueNode $genericTagValueNode): void
    {
        $matches = Strings::matchAll($genericTagValueNode->value, self::PARTIAL_CLASS_REFERENCE_REGEX);

        $resolveFullyQualifiedNames = [];

        foreach ($matches as $match) {
            $resolveFullyQualifiedName = $this->resolveShortNamesToFullyQualified(
                $match[self::CLASS_SNIPPET_PART],
                $this->className
            );

            if ($resolveFullyQualifiedName === null) {
                continue;
            }

            $resolveFullyQualifiedNames[] = $resolveFullyQualifiedName;
        }

        $genericTagValueNode->setAttribute(AttributeKey::REFERENCED_CLASSES, $resolveFullyQualifiedNames);
    }
}
