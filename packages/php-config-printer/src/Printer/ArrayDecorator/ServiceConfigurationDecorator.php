<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Printer\ArrayDecorator;

use Symplify\PhpConfigPrinter\Reflection\ConstantNameFromValueResolver;

final class ServiceConfigurationDecorator
{
    public function __construct(
        private ConstantNameFromValueResolver $constantNameFromValueResolver,
    ) {
    }

    public function decorate(mixed $configuration, string $class): mixed
    {
        if (! is_array($configuration)) {
            return $configuration;
        }

        return $this->decorateClassConstantKeys($configuration, $class);
    }

    /**
     * @param array<string, mixed> $configuration
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
}
