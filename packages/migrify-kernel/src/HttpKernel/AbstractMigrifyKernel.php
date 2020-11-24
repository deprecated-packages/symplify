<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\HttpKernel;

use Nette\Utils\Strings;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;

abstract class AbstractMigrifyKernel extends Kernel
{
    public function getUniqueKernelHash(): string
    {
        $finalKernelClass = static::class;
        $shortClassName = (string) Strings::after($finalKernelClass, '\\', -1);
        return $this->camelCaseToGlue($shortClassName, '_');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash() . '_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle()];
    }

    private function camelCaseToGlue(string $input, string $glue): string
    {
        if ($input === strtolower($input)) {
            return $input;
        }

        $matches = Strings::matchAll($input, '#([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)#');
        $parts = [];
        foreach ($matches as $match) {
            $parts[] = $match[0] === strtoupper($match[0]) ? strtolower($match[0]) : lcfirst($match[0]);
        }

        return implode($glue, $parts);
    }
}
