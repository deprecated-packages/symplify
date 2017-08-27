<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DependencyInjection;

use DateTime;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

final class NoClassInstantiationSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use service and constructor injection rather than instantiation with new %s.';

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
        'Route'

    ];

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

        if ($this->isTestFile($file->getFilename())) {
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

        foreach ($this->allowedClassSuffixes as $allowedClassSuffix) {
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

    private function isTestFile(string $filename): bool
    {
        if (Strings::endsWith($filename, 'Test.php')) {
            return true;
        }

        if (Strings::endsWith($filename, '.phpt')) {
            return true;
        }

        return false;
    }

    private function isEntityClass(string $class, int $classTokenPosition): bool
    {
        $fqnClassName = $this->getFqnClassName($class, $classTokenPosition);

        if (class_exists($fqnClassName)) {
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
            $nextTokenPointer++;
            $classNameParts[] = $this->tokens[$nextTokenPointer]['content'];
            $nextTokenPointer++;
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
}
