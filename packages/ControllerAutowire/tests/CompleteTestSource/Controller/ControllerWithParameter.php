<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Tests\CompleteTestSource\Controller;

final class ControllerWithParameter
{
    /**
     * @var string
     */
    private $kernelRootDir;

    public function __construct(string $kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    public function someAction(): void
    {
    }

    public function getKernelRootDir(): string
    {
        return $this->kernelRootDir;
    }
}
