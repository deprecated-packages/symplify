<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Printer\ArrayDecorator;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use Symplify\PhpConfigPrinter\NodeFactory\NewValueObjectFactory;
use Symplify\PhpConfigPrinter\Reflection\ConstantNameFromValueResolver;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

final class ServiceConfigurationDecorator
{
    public function __construct(
        private ConstantNameFromValueResolver $constantNameFromValueResolver,
        private NewValueObjectFactory $newValueObjectFactory
    ) {
    }

    /**
     * @param mixed|mixed[] $configuration
     * @return mixed|mixed[]
     */
    public function decorate($configuration, string $class, bool $shouldUseConfigureMethod)
    {
        if (! is_array($configuration)) {
            return $configuration;
        }

        $configuration = $this->decorateClassConstantKeys($configuration, $class);

        foreach ($configuration as $key => $value) {
            if ($this->isArrayOfObjects($value)) {
                $configuration = $this->configureArrayOfObjects(
                    $configuration,
                    $value,
                    $key,
                    $shouldUseConfigureMethod
                );
            } elseif (is_object($value)) {
                $configuration[$key] = $this->decorateValueObject($value, $shouldUseConfigureMethod);
            }
        }

        return $configuration;
    }

    /**
     * @param mixed[] $value
     * @return mixed[]
     */
    private function configureArrayOfObjects(
        array $configuration,
        array $value,
        int|string $key,
        bool $shouldUseConfigureMethod
    ): array {
        foreach ($value as $keyValue => $singleValue) {
            if (is_string($keyValue)) {
                $configuration[$key] = array_merge($configuration[$key], [
                    $keyValue => $this->decorateValueObject($singleValue, $shouldUseConfigureMethod),
                ]);
            }

            if (is_numeric($keyValue)) {
                $configuration[$key] = $this->decorateValueObjects([$singleValue], $shouldUseConfigureMethod);
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

    private function decorateValueObject(object $value, bool $shouldUseConfigureMethod): StaticCall|New_
    {
        $new = $this->newValueObjectFactory->create($value);
        if ($shouldUseConfigureMethod) {
            return $new;
        }

        $args = [new Arg($new)];
        return $this->createInlineStaticCall($args);
    }

    /**
     * @param mixed[] $values
     */
    private function decorateValueObjects(array $values, bool $shouldUseConfigureMethod): StaticCall|Array_
    {
        $arrayItems = [];

        foreach ($values as $value) {
            $new = $this->newValueObjectFactory->create($value);
            $arrayItems[] = new ArrayItem($new);
        }

        $array = new Array_($arrayItems);
        if ($shouldUseConfigureMethod) {
            return $array;
        }

        $args = [new Arg($array)];

        return $this->createInlineStaticCall($args);
    }

    private function isArrayOfObjects(mixed $values): bool
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

    /**
     * Depends on symplify/symfony-php-config
     *
     * @param Arg[] $args
     */
    private function createInlineStaticCall(array $args): StaticCall
    {
        $fullyQualified = new FullyQualified(ValueObjectInliner::class);

        return new StaticCall($fullyQualified, 'inline', $args);
    }
}
