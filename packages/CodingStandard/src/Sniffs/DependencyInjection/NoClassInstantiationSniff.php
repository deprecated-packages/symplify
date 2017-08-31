<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DependencyInjection;

use DateTime;
use DateTimeImmutable;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SplFileInfo;
use stdClass;
use Symplify\CodingStandard\Helper\Naming;

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
        DateTimeImmutable::class,
        SplFileInfo::class,
        stdClass::class,
        'Nette\Utils\Html',

        // Symfony Console
        'Symfony\Component\Console\Input\InputArgument',
        'Symfony\Component\Console\Input\InputDefinition',
        'Symfony\Component\Console\Input\InputOption',
        'Symfony\Component\Console\Helper\Table',

        // Nette DI
        'Nette\DI\Config\Loader',

        // Symfony DependencyInjection
        'Symfony\Component\DependencyInjection\Loader\YamlFileLoader',
        'Symfony\Component\Config\FileLocator',

        // php-cs-fixer
        'PhpCsFixer\Tokenizer\Token',
        'PhpCsFixer\FixerDefinition\CodeSample',
        'PhpCsFixer\FixerDefinition\FixerDefinition',
        'PhpCsFixer\FixerConfiguration\FixerOptionBuilder',
        'PhpCsFixer\FixerConfiguration\FixerConfigurationResolver',
        'PhpCsFixer\DocBlock\DocBlock',

        // PHP_CodeSniffer
        'PHP_CodeSniffer\Util\Tokens',
        'PHP_CodeSniffer\Tokenizers\PHP',

        // suffixes
        '*Response',
        '*Exception',
        '*Route',
        '*Event',
        '*Iterator',
        '*Reference', // Symfony DI Reference class
        '*ContainerFactory',

        // prefixes
        'Reflection*',
    ];

    /**
     * @var string[]
     */
    public $extraAllowedClasses = [];

    /**
     * @var string[]
     */
    public $allowedFileClasses = [
        '*Extension', // Symfony and Nette DI Extension classes
        '*Factory', // in factories "new" is expected
        // Symfony DI bootstrap
        '*Bundle',
        '*Kernel',
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
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    public function process(File $file, $position): void
    {
        $this->file = $file;

        if ($this->shouldSkipFile()) {
            return;
        }

        $classNameTokenPosition = TokenHelper::findNext($file, [T_STRING], $position, $position + 3);
        if ($classNameTokenPosition === null) {
            return;
        }

        $className = Naming::getClassName($this->file, $classNameTokenPosition);
        if ($this->isClassInstantiationAllowed($className, $classNameTokenPosition)) {
            return;
        }

        $file->addError(sprintf(self::ERROR_MESSAGE, $className), $position, self::class);
    }

    private function isClassInstantiationAllowed(string $class, int $classTokenPosition): bool
    {
        $allowedClasses = array_merge($this->allowedClasses, $this->extraAllowedClasses);

        foreach ($allowedClasses as $allowedClass) {
            if (fnmatch($allowedClass, $class, FNM_NOESCAPE)) {
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
        $className = Naming::getClassName($this->file, $classTokenPosition);

        if (class_exists($className)) {
            $classReflection = new ReflectionClass($class);
            $docComment = $classReflection->getDocComment();

            return Strings::contains($docComment, '@ORM\Entity');
        }

        return false;
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

        foreach ($this->allowedFileClasses as $allowedFileClass) {
            if (fnmatch($allowedFileClass, $fileClassName,FNM_NOESCAPE)) {
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
