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
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
use Symplify\PHPStanRules\ValueObject\ClassConstantReference;
use Symplify\PHPStanRules\ValueObject\MethodCallReference;
use Symplify\SimplePhpDocParser\PhpDocNodeVisitor\AbstractPhpDocNodeVisitor;

final class ClassReferencePhpDocNodeVisitor extends AbstractPhpDocNodeVisitor
{
    /**
     * @var string
     */
    private const CLASS_SNIPPET_PART = 'class_snippet';

    /**
     * @var string
     */
    private const REFERENCE_PART = 'reference';

    /**
     * @var string
     * @see https://regex101.com/r/2OYung/1
     */
    private const PARTIAL_CLASS_REFERENCE_REGEX = '#(?<' . self::CLASS_SNIPPET_PART . '>[A-Za-z_\\\\]+)::(?<' . self::REFERENCE_PART . '>class|[A-Za-z_]+(\((.*?)?\))?)#';

    private ?string $className = null;

    public function __construct(
        private ReflectionProvider $reflectionProvider
    ) {
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
        if ($this->className === null) {
            throw new ShouldNotHappenException();
        }

        $shortClassName = trim($phpDocTagNode->name, '@');

        // lowercased, probably non class annotation
        if (strtolower($shortClassName) === $shortClassName) {
            return;
        }

        $resolvedFullyQualifiedName = $this->resolveShortNamesToFullyQualified($shortClassName, $this->className);
        if ($resolvedFullyQualifiedName) {
            $phpDocTagNode->name = $resolvedFullyQualifiedName;
        }
    }

    private function processGenericTagValueNode(GenericTagValueNode $genericTagValueNode): void
    {
        if ($this->className === null) {
            throw new ShouldNotHappenException();
        }

        $matches = Strings::matchAll($genericTagValueNode->value, self::PARTIAL_CLASS_REFERENCE_REGEX);

        $resolveFullyQualifiedNames = [];
        $referencedClassConstants = [];
        $referencedMethodCalls = [];

        foreach ($matches as $match) {
            $resolveFullyQualifiedName = $this->resolveShortNamesToFullyQualified(
                $match[self::CLASS_SNIPPET_PART],
                $this->className
            );

            if ($resolveFullyQualifiedName === null) {
                continue;
            }

            $referencePart = $match[self::REFERENCE_PART];
            if ($referencePart === 'class') {
                $resolveFullyQualifiedNames[] = $resolveFullyQualifiedName;
                continue;
            }

            // method call
            if (Strings::contains($referencePart, '(')) {
                $referencePart = trim($referencePart, '()');
                $referencedMethodCalls[] = new MethodCallReference($resolveFullyQualifiedName, $referencePart);
                continue;
            }

            // constant reference
            $referencedClassConstants[] = new ClassConstantReference(
                $resolveFullyQualifiedName,
                $match[self::REFERENCE_PART]
            );
        }

        $genericTagValueNode->setAttribute(AttributeKey::REFERENCED_CLASSES, $resolveFullyQualifiedNames);
        $genericTagValueNode->setAttribute(AttributeKey::REFERENCED_CLASS_CONSTANTS, $referencedClassConstants);
        $genericTagValueNode->setAttribute(AttributeKey::REFERENCED_METHOD_CALLS, $referencedMethodCalls);
    }
}
