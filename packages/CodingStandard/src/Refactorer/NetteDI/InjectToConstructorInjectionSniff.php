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
    private const ERROR_MESSAGE = 'Use constructor injection instead of @inject annotation or inject*() methods.';

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
        $this->processClassMethods();
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

    private function processClassMethods(): void
    {
        $methods = $this->classWrapper->getMethods();
        foreach ($methods as $method) {
            if (! $method->hasNamePrefix('inject')) {
                continue;
            }

            $fix = $this->addInjectError($method->getPosition());
            if ($fix) {
                $this->fixInjectMethod($method);
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

        // $propertyWrapper->getAnnotation('var');
        // $propertyWrapper->geType();

        // 3. add dependency to constructor
        $constructMethod = $this->classWrapper->getMethod('__construct');
        if (! $constructMethod) {
            $type = $propertyWrapper->getType();
            $name = $propertyWrapper->getName();
            $this->classWrapper->addConstructorMethodWithProperty($type, $name);
        }
    }

    private function fixInjectMethod(MethodWrapper $method): void
    {
        // 1. detect parameters
        $injectedParameters = [];
        foreach ($method->getParameters() as $parameter) {
            $injectedParameters[] = [
                'name' => $parameter->getParamterName(),
                'type' => $parameter->getParamterType()
            ];
        }

        // 2. remove inject method
        $method->remove();
        $this->addParametersToConstructor($injectedParameters);
    }

    /**
     * @param mixed[] $injectedParameters
     */
    private function addParametersToConstructor(array $injectedParameters): void
    {
        $constructMethod = $this->classWrapper->getMethod('__construct');
        if (! $constructMethod) {
            foreach ($injectedParameters as $injectedParameter) {
                $type = $injectedParameter['type'];
                $name = $injectedParameter['name'];
                $this->classWrapper->addConstructorMethodWithProperty($type, $name);
            }
        }

        // @todo for existing constructor
    }
}
