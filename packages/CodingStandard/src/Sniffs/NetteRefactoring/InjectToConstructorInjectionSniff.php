<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Sniffs\NetteRefactoring;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\CodingStandard\TokenWrapper\ClassWrapper;
use Symplify\CodingStandard\TokenWrapper\MethodWrapper;
use Symplify\CodingStandard\TokenWrapper\PropertyWrapper;

/**
 * Constructor injection should be used over @inject annotation and inject* methods. Except abstract BasePresenter.
 */
final class InjectToConstructorInjectionSniff implements Sniff
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var ClassWrapper
     */
    private $classWrapper;

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
            if ($property->hasAnnotation('@inject')) {
                $fix = $this->addInjectAnnotationError($property->getPosition());
                if ($fix) {
                    $this->fixInjectAnnotation($property);
                }
            }
        }
    }

    private function processClassMethods(): void
    {
        $methods = $this->classWrapper->getMethods();
        foreach ($methods as $method) {
            if ($method->hasNamePrefix('inject')) {
                $fix = $this->addInjectMethodError($method->getPosition());
                if ($fix) {
                    $this->fixInjectMethod($method);
                }
            }
        }
    }

    private function addInjectAnnotationError(int $position): bool
    {
        return $this->file->addFixableError(
            'Constructor injection should be used over @inject annotation (except abstract BasePresenter).',
            $position,
            self::class
        );
    }

    private function addInjectMethodError(int $position): bool
    {
        return $this->file->addFixableError(
            'Constructor injection should be used over inject* method (except abstract BasePresenter).',
            $position,
            self::class
        );
    }

    private function fixInjectAnnotation(PropertyWrapper $propertyWrapper): void
    {
        // 1. remove @inject
        $propertyWrapper->removeAnnotation('@inject');

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

        // 3. add parameters to constructor
        $constructMethod = $this->classWrapper->getMethod('__construct');
        if (! $constructMethod) {
            foreach ($injectedParameters as $injectedParameter) {
                $type = $injectedParameter['type'];
                $name = $injectedParameter['name'];
                $this->classWrapper->addConstructorMethodWithProperty($type, $name);
            }
        }
    }
}
