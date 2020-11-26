<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Printer\ArrayDecorator;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use Symplify\PhpConfigPrinter\NodeFactory\NewValueObjectFactory;
use Symplify\PhpConfigPrinter\Reflection\ConstantNameFromValueResolver;
use Symplify\SymfonyPhpConfig\ValueObjectInliner;

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

    public function __construct(
        ConstantNameFromValueResolver $constantNameFromValueResolver,
        NewValueObjectFactory $newValueObjectFactory
    ) {
        $this->constantNameFromValueResolver = $constantNameFromValueResolver;
        $this->newValueObjectFactory = $newValueObjectFactory;
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

    private function decorateValueObject(object $value): StaticCall
    {
        $new = $this->newValueObjectFactory->create($value);
        $args = [new Arg($new)];

        return $this->createInlineStaticCall($args);
    }

    private function decorateValueObjects(array $values): StaticCall
    {
        $arrayItems = [];
        foreach ($values as $value) {
            $new = $this->newValueObjectFactory->create($value);
            $arrayItems[] = new ArrayItem($new);
        }

        $array = new Array_($arrayItems);
        $args = [new Arg($array)];

        return $this->createInlineStaticCall($args);
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
