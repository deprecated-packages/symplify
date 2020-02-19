<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

final class Naming
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @var string[]
     */
    private const CLASS_NAMES_BY_FILE_PATH = [];

    /**
     * @var mixed[][]
     */
    private $referencedNamesByFilePath = [];

    /**
     * @var string[][]
     */
    private $fqnClassNameByFilePathAndClassName = [];

    public function getFileClassName(File $file): ?string
    {
        // get name by path
        if (isset(self::CLASS_NAMES_BY_FILE_PATH[$file->path])) {
            return self::CLASS_NAMES_BY_FILE_PATH[$file->path];
        }

        $classPosition = TokenHelper::findNext($file, T_CLASS, 1);
        if ($classPosition === null) {
            return null;
        }

        $className = ClassHelper::getFullyQualifiedName($file, $classPosition);

        return ltrim($className, '\\');
    }

    public function getClassName(File $file, int $classNameStartPosition): string
    {
        $tokens = $file->getTokens();

        $firstNamePart = $tokens[$classNameStartPosition]['content'];

        // is class <name>
        if ($this->isClassName($file, $classNameStartPosition)) {
            $namespace = NamespaceHelper::findCurrentNamespaceName($file, $classNameStartPosition);
            if ($namespace) {
                return $namespace . '\\' . $firstNamePart;
            }

            return $firstNamePart;
        }

        $classNameParts = [];
        $classNameParts[] = $firstNamePart;

        $nextTokenPointer = $classNameStartPosition + 1;
        while ($tokens[$nextTokenPointer]['code'] === T_NS_SEPARATOR) {
            ++$nextTokenPointer;
            $classNameParts[] = $tokens[$nextTokenPointer]['content'];
            ++$nextTokenPointer;
        }

        $completeClassName = implode(self::NAMESPACE_SEPARATOR, $classNameParts);

        $fqnClassName = self::getFqnClassName($file, $completeClassName, $classNameStartPosition);
        if ($fqnClassName !== '') {
            return ltrim($fqnClassName, self::NAMESPACE_SEPARATOR);
        }

        return $completeClassName;
    }

    private function getFqnClassName(File $file, string $className, int $classTokenPosition): string
    {
        $referencedNames = $this->getReferencedNames($file);

        foreach ($referencedNames as $referencedName) {
            if (isset($this->fqnClassNameByFilePathAndClassName[$file->path][$className])) {
                return $this->fqnClassNameByFilePathAndClassName[$file->path][$className];
            }

            $resolvedName = NamespaceHelper::resolveClassName(
                $file,
                $referencedName->getNameAsReferencedInFile(),
                $classTokenPosition
            );

            if ($referencedName->getNameAsReferencedInFile() === $className) {
                $this->fqnClassNameByFilePathAndClassName[$file->path][$className] = $resolvedName;

                return $resolvedName;
            }
        }

        return '';
    }

    /**
     * As in:
     * class <name>
     */
    private function isClassName(File $file, int $position): bool
    {
        return (bool) $file->findPrevious(T_CLASS, $position, max(1, $position - 3));
    }

    /**
     * @return mixed[]
     */
    private function getReferencedNames(File $file): array
    {
        if (isset($this->referencedNamesByFilePath[$file->path])) {
            return $this->referencedNamesByFilePath[$file->path];
        }

        $referencedNames = ReferencedNameHelper::getAllReferencedNames($file, 0);

        $this->referencedNamesByFilePath[$file->path] = $referencedNames;

        return $referencedNames;
    }
}
