<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Commenting;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\TypeHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use Symplify\PackageBuilder\Types\ClassLikeExistenceChecker;

/**
 * @inspiration https://github.com/slevomat/coding-standard/blob/90dbcb3258dd1dcd5fa7d960a8bd30c6cb915b3a/SlevomatCodingStandard/Sniffs/Namespaces/FullyQualifiedClassNameInAnnotationSniff.php
 *
 * @deprecated
 */
final class AnnotationTypeExistsSniff implements Sniff
{
    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    public function __construct(ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;

        trigger_error(sprintf(
            'Sniff "%s" is deprecated and will be removed in Symplify 8 (May 2020). Use "%s" and "%s" instead',
            self::class,
            'https://github.com/phpstan/phpstan-src/blob/master/src/Rules/Properties/ExistingClassesInPropertiesRule.php',
            'https://github.com/phpstan/phpstan-src/blob/master/src/Rules/Functions/ExistingClassesInTypehintsRule.php'
        ));

        sleep(3);
    }

    /**
     * @return int[]|string[]
     */
    public function register(): array
    {
        return [T_DOC_COMMENT_OPEN_TAG];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $annotations = AnnotationHelper::getAnnotations($file, $position);

        foreach ($annotations as $annotationName => $annotationByName) {
            if (! $this->isTypeAnnotation($annotationName)) {
                continue;
            }

            /** @var Annotation $annotation */
            foreach ($annotationByName as $annotation) {
                if ($annotation->getContent() === null) {
                    continue;
                }

                $typeHints = $this->resolveTypes($annotation, $annotationName);

                $this->processTypeHints($file, $position, $typeHints, $annotation, $annotationName);
            }
        }
    }

    private function isTypeAnnotation(string $annotationName): bool
    {
        return in_array($annotationName, ['@var', '@param', '@return', '@throws'], true);
    }

    /**
     * @return string[]
     */
    private function resolveTypes(Annotation $annotation, string $annotationName): array
    {
        $annotationContent = $annotation->getContent();
        if ($annotationContent === null) {
            return [];
        }

        $typeHintsDefinition = Strings::split($annotationContent, '#\\s+#')[0];

        if ($annotationName === '@var') {
            $match = Strings::match($annotationContent, '#^\$\\S+\\s+(.+)#');
            if (isset($match[1]) && $match[1]) {
                $typeHintsDefinition = $match[1];
            }
        }

        return explode('|', $typeHintsDefinition);
    }

    /**
     * @param string[] $typeHints
     */
    private function processTypeHints(
        File $file,
        int $position,
        array $typeHints,
        Annotation $annotation,
        string $annotationName
    ): void {
        foreach ($typeHints as $typeHint) {
            if ($this->shouldSkipTypeHint($typeHint)) {
                continue;
            }

            $fullyQualifiedTypeHint = TypeHintHelper::getFullyQualifiedTypeHint(
                $file,
                $annotation->getStartPointer(),
                $typeHint
            );

            $fullyQualifiedTypeHint = ltrim($fullyQualifiedTypeHint, '\\');
            if ($this->classLikeExistenceChecker->exists($fullyQualifiedTypeHint)) {
                continue;
            }

            $file->addError(
                sprintf(
                    '"%s" annotation type "%s" (fully qualified: "%s") does not exist.',
                    $annotationName,
                    $typeHint,
                    $fullyQualifiedTypeHint
                ),
                $position,
                self::class
            );
        }
    }

    private function shouldSkipTypeHint(string $typeHint): bool
    {
        $lowercasedTypeHint = strtolower($typeHint);

        if (TypeHintHelper::isSimpleTypeHint($lowercasedTypeHint)) {
            return true;
        }

        if (TypeHintHelper::isSimpleUnofficialTypeHints($lowercasedTypeHint)) {
            return true;
        }

        return ! TypeHelper::isTypeName($typeHint);
    }
}
