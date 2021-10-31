<?php

declare(strict_types=1);

namespace Symplify\SymfonyContainerBuilder\Parameters;

/**
 * Mimics some "kernel.*" parameters
 */
final class KernelParametersProvider
{
    private string|null $projectDir = null;

    /**
     * @return array<string, mixed>
     */
    public function provide(): array
    {
        return [
            'kernel.project_dir' => $this->getProjectDir(),
        ];
    }

    /**
     * Copied from \Symfony\Component\HttpKernel\Kernel::getProjectDir()
     */
    private function getProjectDir(): string
    {
        if ($this->projectDir === null) {
            $reflectionObject = new \ReflectionObject($this);

            $dir = $reflectionObject->getFileName();
            if (! is_file($dir)) {
                throw new \LogicException(sprintf(
                    'Cannot auto-detect project dir for kernel of class "%s".',
                    $reflectionObject->name
                ));
            }

            $dir = $rootDir = \dirname($dir);
            while (! is_file($dir . '/composer.json')) {
                if ($dir === \dirname($dir)) {
                    return $this->projectDir = $rootDir;
                }
                $dir = \dirname($dir);
            }
            $this->projectDir = $dir;
        }

        return $this->projectDir;
    }
}
