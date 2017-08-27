<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\DependencyInjection;

use DateTime;
use Nette\Utils\Strings;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use ReflectionClass;
use SlevomatCodingStandard\Helpers\TokenHelper;

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

        if ($this->isTestFile($file->getFilename())) {
            return;
        }

        $classNameTokenPosition = TokenHelper::findNext($file, [T_STRING], $position);
        if ($classNameTokenPosition === null) {
            return;
        }

        $tokens = $file->getTokens();
        $classNameToken = $tokens[$classNameTokenPosition];
        $className = $classNameToken['content'];

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

        if ($this->isEntityClass($class, $classTokenPosition)) {
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
        // @todo: resolve FQN
        if (class_exists($class)) {
            $classReflection = new ReflectionClass($class);
            $docComment = $classReflection->getDocComment();

            return Strings::contains($docComment, '@ORM\Entity');
        }

        return false;
    }
}
