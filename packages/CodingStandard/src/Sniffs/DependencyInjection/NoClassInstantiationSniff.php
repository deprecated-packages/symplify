<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DependencyInjection;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use Symplify\CodingStandard\TokenRunner\Analyzer\SnifferAnalyzer\Naming;
use Symplify\PackageBuilder\Types\ClassLikeExistenceChecker;

final class NoClassInstantiationSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use service and constructor injection rather than instantiation with "new %s".';

    /**
     * @var bool
     */
    public $includeEntities = false;

    /**
     * @var string[]
     */
    public $allowedClasses = [
        // PHP internal classes
        'DateTime*',
        'std*',
        'Spl*',
        'Reflection*',

        // Nette
        'Nette\Utils\Html',
        'Nette\Loaders\RobotLoader',
        'Nette\Configurator',
        'Nette\DI\Config\Loader',
        '*DateTime',

        // Symplify
        'Symplify\Autodiscovery\*',
        'Symplify\FlexLoader\*',
        'Symplify\EasyCodingStandard\Error\Error',
        'Symplify\EasyCodingStandard\Yaml\*',
        'Symplify\PackageBuilder\*',

        'GuzzleHttp\Psr7\Request',
        'PharIo\Version\Version',
        'phpDocumentor\Reflection\Fqsen',

        // Symfony
        'Symfony\Component\Console\Descriptor\TextDescriptor',
        'Symfony\Component\Console\Input\*',
        'Symfony\Component\Console\Helper\Table',
        'Symfony\Component\DependencyInjection\*',
        'Symfony\Component\Config\*',
        '*Exception',
        '*Constraint',

        // Symfony misc
        'Symfony\Component\Process\Process',

        // php-cs-fixer
        'PhpCsFixer\*',

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

        // value objects or build elements, like SplFileInfo
        '*Info',
        '*Node',
        '*Request',
        '*Response',
        '*Collection',
        '*Resource',
        '*Error',
        '*Token',

        // Doctrine
        'Doctrine\ORM\Query\Expr',

        // misc
        'K0nias\FakturoidApi\*',
        'setasign\Fpdi\*',
        'ZipStream\ZipStream',
        # pre-container used classes
        'Symplify\Autodiscovery\Util\*',
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
        '*CompilerPass',
    ];

    /**
     * @var File
     */
    private $file;

    /**
     * @var Naming
     */
    private $naming;

    /**
     * @var ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;

    public function __construct(Naming $naming, ClassLikeExistenceChecker $classLikeExistenceChecker)
    {
        $this->naming = $naming;
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
    }

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_NEW];
    }

    /**
     * @param int $position
     */
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

        $className = $this->naming->getClassName($this->file, $classNameTokenPosition);
        if ($this->isClassInstantiationAllowed($className, $classNameTokenPosition)) {
            return;
        }

        $file->addError(sprintf(self::ERROR_MESSAGE, $className), $position, self::class);
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

        if ($this->isBootstrapFile()) {
            return true;
        }

        if ($this->isTestFile()) {
            return true;
        }

        return false;
    }

    private function isClassInstantiationAllowed(string $class, int $classTokenPosition): bool
    {
        $allowedClasses = array_merge($this->allowedClasses, $this->extraAllowedClasses);

        foreach ($allowedClasses as $allowedClass) {
            if (fnmatch($allowedClass, $class, FNM_NOESCAPE)) {
                return true;
            }
        }

        return ! $this->includeEntities && $this->isEntityClass($class, $classTokenPosition);
    }

    private function isTrait(): bool
    {
        return (bool) $this->file->findNext(T_TRAIT, 1);
    }

    private function isAllowedFileClass(): bool
    {
        $fileClassName = $this->getFileClassName();
        if ($fileClassName === null) {
            return false;
        }

        foreach ($this->allowedFileClasses as $allowedFileClass) {
            if (fnmatch($allowedFileClass, $fileClassName, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }

    private function isBinFile(): bool
    {
        return Strings::contains($this->file->getFilename(), DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR)
            || Strings::startsWith($this->file->getFilename(), 'bin' . DIRECTORY_SEPARATOR);
    }

    private function isBootstrapFile(): bool
    {
        return Strings::endsWith($this->file->getFilename(), 'bootstrap.php');
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

    private function isEntityClass(string $class, int $classTokenPosition): bool
    {
        $className = $this->naming->getClassName($this->file, $classTokenPosition);

        if (! $this->classLikeExistenceChecker->exists($className)) {
            return false;
        }

        $docComment = (new ReflectionClass($class))->getDocComment();
        if ($docComment === false) {
            return false;
        }

        return Strings::contains($docComment, '@ORM\Entity');
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
