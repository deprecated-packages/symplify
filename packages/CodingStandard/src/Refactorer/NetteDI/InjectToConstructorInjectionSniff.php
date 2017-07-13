<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Refactorer\NetteDI;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;
use Symplify\CodingStandard\TokenWrapper\MethodWrapper;
use Symplify\CodingStandard\TokenWrapper\PropertyWrapper;

final class InjectToConstructorInjectionSniff implements Sniff
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use constructor injection instead of @inject annotation methods.';

    /**
     * @var string
     */
    private const INJECT_ANNOTATION = '@inject';

    /**
     * @var File
     */
    private $file;

    /**
     * @var ClassWrapper
     */
    private $classWrapper;

    /**
     * @return int[]
     */
    public function register(): array
    {
        return [T_CLASS];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position): void
    {
        $this->file = $file;
        $this->classWrapper = ClassWrapper::createFromFileAndPosition($file, $position);

        if ($this->isClassBasePresenter()) {
            return;
        }

        $this->processClassProperties();
    }

    private function isClassBasePresenter(): bool
    {
        return $this->isClassPresenter() && $this->classWrapper->isAbstract();
    }

    private function isClassPresenter(): bool
    {
        return $this->classWrapper->hasNameSuffix('Presenter');
    }

    private function processClassProperties(): void
    {
        $properties = $this->classWrapper->getProperties();
        foreach ($properties as $property) {
            if (! $property->hasAnnotation(self::INJECT_ANNOTATION)) {
                continue;
            }

            $fix = $this->addInjectError($property->getPosition());
            if ($fix) {
                $this->fixInjectAnnotation($property);
            }
        }
    }

    private function addInjectError(int $position): bool
    {
        return $this->file->addFixableError(self::ERROR_MESSAGE, $position, self::class);
    }

    private function fixInjectAnnotation(PropertyWrapper $propertyWrapper): void
    {
        // 1. remove @inject
        $propertyWrapper->removeAnnotation(self::INJECT_ANNOTATION);

        // 2. set visibility to private
        $propertyWrapper->changeAccesibilityToPrivate();

        // 3. add dependency to constructor
        $constructMethod = $this->classWrapper->getMethod('__construct');
        if (! $constructMethod) {
            $type = $propertyWrapper->getType();
            $name = $propertyWrapper->getName();
            $this->classWrapper->addConstructorMethodWithProperty($type, $name);
        }
    }
}
