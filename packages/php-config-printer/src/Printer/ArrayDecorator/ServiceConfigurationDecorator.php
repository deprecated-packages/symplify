<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Printer\ArrayDecorator;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\PhpConfigPrinter\NodeFactory\NewValueObjectFactory;
use Symplify\PhpConfigPrinter\Reflection\ConstantNameFromValueResolver;
use Symplify\PhpConfigPrinter\ValueObject\Option;

final class ServiceConfigurationDecorator
{
    /**
     * @var ConstantNameFromValueResolver
     */
    private $constantNameFromValueResolver;

    /**
     * @var NewValueObjectFactory
     */
    private $newValueObjectFactory;

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    public function __construct(
        ConstantNameFromValueResolver $constantNameFromValueResolver,
        NewValueObjectFactory $newValueObjectFactory,
        ParameterProvider $parameterProvider
    ) {
        $this->constantNameFromValueResolver = $constantNameFromValueResolver;
        $this->newValueObjectFactory = $newValueObjectFactory;
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @param mixed|mixed[] $configuration
     * @return mixed|mixed[]
     */
    public function decorate($configuration, string $class)
    {
        if (! is_array($configuration)) {
            return $configuration;
        }

        $configuration = $this->decorateClassConstantKeys($configuration, $class);

        foreach ($configuration as $key => $value) {
            if ($this->isArrayOfObjects($value)) {
                $configuration[$key] = $this->decorateValueObjects($value);
            } elseif (is_object($value)) {
                $configuration[$key] = $this->decorateValueObject($value);
            }
        }

        return $configuration;
    }

    /**
     * @param mixed[] $configuration
     * @return mixed[]
     */
    private function decorateClassConstantKeys(array $configuration, string $class): array
    {
        foreach ($configuration as $key => $value) {
            $constantName = $this->constantNameFromValueResolver->resolveFromValueAndClass($key, $class);
            if ($constantName === null) {
                continue;
            }

            unset($configuration[$key]);

            $classConstantReference = $class . '::' . $constantName;
            $configuration[$classConstantReference] = $value;
        }

        return $configuration;
    }

    private function decorateValueObject(object $value): FuncCall
    {
        $new = $this->newValueObjectFactory->create($value);
        $args = [new Arg($new)];

        $functionName = $this->parameterProvider->provideStringParameter(Option::INLINE_VALUE_OBJECT_FUNC_CALL_NAME);

        return new FuncCall(new FullyQualified($functionName), $args);
    }

    private function decorateValueObjects(array $values): FuncCall
    {
        $arrayItems = [];
        foreach ($values as $value) {
            $new = $this->newValueObjectFactory->create($value);
            $arrayItems[] = new ArrayItem($new);
        }

        $array = new Array_($arrayItems);
        $args = [new Arg($array)];

        $functionName = $this->parameterProvider->provideStringParameter(Option::INLINE_VALUE_OBJECTS_FUNC_CALL_NAME);

        return new FuncCall(new FullyQualified($functionName), $args);
    }

    private function isArrayOfObjects($values): bool
    {
        if (! is_array($values)) {
            return false;
        }

        if ($values === []) {
            return false;
        }

        foreach ($values as $value) {
            if (! is_object($value)) {
                return false;
            }
        }

        return true;
    }
}
