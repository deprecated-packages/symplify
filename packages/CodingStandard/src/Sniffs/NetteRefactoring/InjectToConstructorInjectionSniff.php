<?php declare(strict_types=1);

namespace SymplifyCodingStandard\Sniffs\NetteRefactoring;

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
     * @var int
     */
    private $position;

    /**
     * @var ClassWrapper
     */
    private $classWrapper;

    public function register() : array
    {
        return [T_CLASS];
    }

    /**
     * @param File $file
     * @param int $position
     */
    public function process(File $file, $position)
    {
        $this->file = $file;
        $this->position = $position;

        $this->classWrapper = ClassWrapper::createFromFileAndPosition($file, $position);

        if ($this->isClassBasePresenter()) {
            return;
        }

//        $this->file->fixer->beginChangeset();

        $this->processClassProperties();
        $this->processClassMethods();

//        $this->file->fixer->endChangeset();
    }

    private function isClassBasePresenter() : bool
    {
        return $this->isClassPresenter() && $this->classWrapper->isAbstract();
    }

    private function isClassPresenter() : bool
    {
        return $this->classWrapper->hasNameSuffix('Presenter');
    }

    private function processClassProperties()
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

    private function processClassMethods()
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

    private function addInjectAnnotationError(int $position) : bool
    {
        return $this->file->addFixableError(
            'Constructor injection should be used over @inject annotation (except abstract BasePresenter).',
            $position
        );
    }

    private function addInjectMethodError(int $position) : bool
    {
        return $this->file->addFixableError(
            'Constructor injection should be used over inject* method (except abstract BasePresenter).',
            $position
        );
    }

    private function fixInjectAnnotation(PropertyWrapper $propertyWrapper)
    {
        // 1. remove @inject
        $propertyWrapper->removeAnnotation('@inject');

        // 2. set visibility to private
        $propertyWrapper->changeAccesibilityToPrivate();

        // $propertyWrapper->getAnnotation('var');
        // $propertyWrapper->geType();

        // 3. add dependency to constructor
        $constructMethod = $this->classWrapper->getMethod('__construct');
        if ($constructMethod) {
            // @todo!
            // $constructMethod->addParameterWithSetter($type, $name);

        } else {
            $type = $propertyWrapper->getType();
            $name = $propertyWrapper->getName();
            $this->classWrapper->addConstructorMethodWithProperty($type, $name);
        }
    }

    private function fixInjectMethod(MethodWrapper $method)
    {
        // 1. detect parameters
        $injectedParameters = [];
        foreach ($method->getParameters() as $parameter) {
            $injectedParameters[] = [
                'name' => $parameter->getParamterName(),
                'type' => $parameter->getParamterType()
            ];
        }

        // 2. add parameters to constructor
        $constructMethod = $this->classWrapper->getMethod('__construct');
        if ($constructMethod) {
            // @todo!
            // $constructMethod->addParameterWithSetter($type, $name);

        } else {
            foreach ($injectedParameters as $injectedParameter) {
                $type = $injectedParameter['type'];
                $name = $injectedParameter['name'];
                $this->classWrapper->addConstructorMethodWithProperty($type, $name);
            }
        }

        // 3. remove
        $method->remove();
//        $this->classWrapper->addConstructorMethodWithProperty($type, $name);
    }
}
