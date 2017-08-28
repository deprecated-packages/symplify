<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DependencyInjection;

use DateTime;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

final class NoClassInstantiationSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use service and constructor injection rather than instantiation with "new %s".';

    /**
     * @var string[]
     */
    public $allowedClasses = [
        DateTime::class,
    ];

    /**
     * @var string[]
     */
    public $allowedClassSuffixes = [
        'Response',
        'Exception',
        'Route',
        'Event',
        'Iterator',
        'Reference' // Symfony DI Reference class
    ];

    /**
     * @var string[]
     */
    public $allowedFileClassSuffixes = [
        'Extension', // Symfony and Nette DI Extension classes
        'Factory', // in factories "new" is expected
    ];

    /**
     * @var string[]
     */
    public $extraAllowedClassSuffixes = [];

    /**
     * @var string[]
     */
    public $allowedClassPrefixes = [
        'Reflection',
    ];

    /**
     * @var bool
     */
    public $includeEntities = false;

    /**
     * @var File
     */
    private $file;

    /**
     * @var mixed[]
     */
    private $tokens = [];

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->tokens = $file->getTokens();

        if ($this->shouldSkipFile()) {
            return;
        }

        $classNameTokenPosition = TokenHelper::findNext($file, [T_STRING], $position);
        if ($classNameTokenPosition === null) {
            return;
        }

        $className = $this->getClassName($classNameTokenPosition);
        if ($this->isClassInstantiationAllowed($className, $classNameTokenPosition)) {
            return;
        }

        $file->addError(sprintf(
            self::ERROR_MESSAGE,
            $className
        ), $position, self::class);
    }

    private function isClassInstantiationAllowed(string $class, int $classTokenPosition): bool
    {
        if (in_array($class, $this->allowedClasses, true)) {
            return true;
        }

        $allowedClassSuffixes = $this->allowedClassSuffixes + $this->extraAllowedClassSuffixes;
        foreach ($allowedClassSuffixes as $allowedClassSuffix) {
            if (Strings::endsWith($class, $allowedClassSuffix)) {
                return true;
            }
        }

        foreach ($this->allowedClassPrefixes as $allowedClassPrefix) {
            if (Strings::startsWith($class, $allowedClassPrefix)) {
                return true;
            }
        }

        if (! $this->includeEntities && $this->isEntityClass($class, $classTokenPosition)) {
            return true;
        }

        return false;
    }




    private function isEntityClass(string $class, int $classTokenPosition): bool
    {
        $className = $this->getClassName($classTokenPosition);

        if (class_exists($className)) {
            // too slow
            // better approach of external class?
            // better reflection?
            $classReflection = new ReflectionClass($class);
            $docComment = $classReflection->getDocComment();

            return Strings::contains($docComment, '@ORM\Entity');
        }

        return false;
    }

    private function getClassName(int $classNameStartPosition): string
    {
        $classNameParts = [];
        $classNameParts[] = $this->tokens[$classNameStartPosition]['content'];

        $nextTokenPointer = $classNameStartPosition + 1;
        while ($this->tokens[$nextTokenPointer]['code'] === T_NS_SEPARATOR) {
            ++$nextTokenPointer;
            $classNameParts[] = $this->tokens[$nextTokenPointer]['content'];
            ++$nextTokenPointer;
        }

        $completeClassName = implode('\\', $classNameParts);

        $fqnClassName = $this->getFqnClassName($completeClassName, $classNameStartPosition);

        return ltrim($fqnClassName, '\\');
    }

    private function getFqnClassName(string $className, int $classTokenPosition): string
    {
        $openTagPointer = TokenHelper::findPrevious($this->file, T_OPEN_TAG, $classTokenPosition);
        $useStatements = UseStatementHelper::getUseStatements($this->file, $openTagPointer);
        $referencedNames = ReferencedNameHelper::getAllReferencedNames($this->file, $openTagPointer);

        foreach ($referencedNames as $referencedName) {
            $resolvedName = NamespaceHelper::resolveClassName(
                $this->file,
                $referencedName->getNameAsReferencedInFile(),
                $useStatements,
                $classTokenPosition
            );

            if (Strings::endsWith($resolvedName, $className)) {
                return $resolvedName;
            }
        }

        return '';
    }

    private function shouldSkipFile(): bool
    {
        if ($this->isTrait()) {
            return true;
        }

        if ($this->isAllowedFileClass()) {
            return true;
        }

        if ($this->isBinFile()) {
            return true;
        }

        if ($this->isTestFile()) {
            return true;
        }

        if ($this->isAppKernel()) {
            return true;
        }

        return false;
    }

    private function isTestFile(): bool
    {
        if (Strings::endsWith($this->file->getFilename(), 'TestCase.php')) {
            return true;
        }

        if (Strings::endsWith($this->file->getFilename(), 'Test.php')) {
            return true;
        }

        if (Strings::endsWith($this->file->getFilename(), '.phpt')) {
            return true;
        }

        return false;
    }

    private function isAppKernel(): bool
    {
        return Strings::endsWith($this->file->getFilename(), 'AppKernel.php');
    }

    private function isTrait(): bool
    {
        return (bool) $this->file->findNext(T_TRAIT, 1);
    }

    private function isBinFile(): bool
    {
        return Strings::contains($this->file->getFilename(), DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR);
    }

    private function isAllowedFileClass(): bool
    {
        $fileClassName = $this->getFileClassName();

        foreach ($this->allowedFileClassSuffixes as $allowedFileClassSuffix) {
            if (Strings::endsWith($fileClassName, 'Factory')) {
                return true;
            }
        }

        return false;
    }

    private function getFileClassName(): ?string
    {
        $classPosition = TokenHelper::findNext($this->file, T_CLASS, 1);
        if ($classPosition === null) {
            return null;
        }

        return ClassHelper::getFullyQualifiedName($this->file, $classPosition);
    }
}
