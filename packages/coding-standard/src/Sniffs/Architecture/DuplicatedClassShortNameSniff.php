<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\Architecture;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\SmartFileSystem\SmartFileInfo;

final class DuplicatedClassShortNameSniff implements Sniff
{
    /**
     * @var string[]
     */
    public $allowedClassNames = [];

    /**
     * @var string[][]
     */
    private $declaredClassesByShortName = [];

    /**
     * @var Naming
     */
    private $naming;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    public function __construct(Naming $naming, ChangedFilesDetector $changedFilesDetector)
    {
        $this->naming = $naming;
        $this->changedFilesDetector = $changedFilesDetector;
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_STRING];
    }

    /**
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $classTokenPosition = $file->findPrevious(T_CLASS, $position, max(1, $position - 3));

        // not a class name
        if ($classTokenPosition === false) {
            return;
        }

        $shortClassName = $file->getTokens()[$position]['content'];
        $fullyQualifiedClassName = $this->naming->getClassName($file, $position);

        // is allowed
        foreach ($this->allowedClassNames as $allowedClassName) {
            if (fnmatch($allowedClassName, $shortClassName, FNM_NOESCAPE)) {
                return;
            }
        }

        $this->prepareDeclaredClassesByShortName();

        $this->declaredClassesByShortName[$shortClassName] = array_unique(
            array_merge([$fullyQualifiedClassName], $this->declaredClassesByShortName[$shortClassName] ?? [])
        );

        if (count($this->declaredClassesByShortName[$shortClassName]) <= 1) {
            return;
        }

        $this->invalidateCacheForCheckedFiles($this->declaredClassesByShortName[$shortClassName]);

        $message = sprintf(
            'Class with base "%s" name is already used in "%s".%sUse specific name to make class unique and easy to recognize from the other.',
            $shortClassName,
            implode('", "', $this->declaredClassesByShortName[$shortClassName]),
            PHP_EOL
        );

        $file->addError($message, $position, self::class);
    }

    private function prepareDeclaredClassesByShortName(): void
    {
        // is defined?
        if ($this->declaredClassesByShortName !== []) {
            return;
        }

        foreach (get_declared_classes() as $className) {
            $shortClassName = Strings::after($className, '\\', -1);

            $this->declaredClassesByShortName[$shortClassName][] = $className;
        }
    }

    private function invalidateCacheForCheckedFiles(array $classesToInvalidate): void
    {
        foreach ($classesToInvalidate as $classToInvalidate) {
            if (! class_exists($classToInvalidate)) {
                continue;
            }

            $classReflection = new ReflectionClass($classToInvalidate);
            $classFileName = $classReflection->getFileName();
            if (! $classFileName) {
                continue;
            }

            $fileInfo = new SmartFileInfo($classFileName);
            $this->changedFilesDetector->invalidateFileInfo($fileInfo);
        }
    }
}
