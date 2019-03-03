<?php declare(strict_types=1);

namespace Symplify\TokenRunner\Analyzer\SnifferAnalyzer;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

final class Naming
{
    /**
     * @var string
     */
    private const NAMESPACE_SEPARATOR = '\\';

    /**
     * @var mixed[][]
     */
    private $useStatementsByFilePath = [];

    /**
     * @var mixed[][]
     */
    private $referencedNamesByFilePath = [];

    /**
     * @var string[][]
     */
    private $fqnClassNameByFilePathAndClassName = [];

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
        if ($fqnClassName) {
            return ltrim($fqnClassName, self::NAMESPACE_SEPARATOR);
        }

        return $completeClassName;
    }

    private function getFqnClassName(File $file, string $className, int $classTokenPosition): string
    {
        $useStatements = $this->getUseStatements($file);
        $referencedNames = $this->getReferencedNames($file);

        foreach ($referencedNames as $referencedName) {
            if (isset($this->fqnClassNameByFilePathAndClassName[$file->path][$className])) {
                return $this->fqnClassNameByFilePathAndClassName[$file->path][$className];
            }

            $resolvedName = NamespaceHelper::resolveClassName(
                $file,
                $referencedName->getNameAsReferencedInFile(),
                $useStatements,
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
    private function getUseStatements(File $file): array
    {
        if (isset($this->useStatementsByFilePath[$file->path])) {
            return $this->useStatementsByFilePath[$file->path];
        }

        $useStatements = UseStatementHelper::getUseStatements($file, 0);

        $this->useStatementsByFilePath[$file->path] = $useStatements;

        return $useStatements;
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
