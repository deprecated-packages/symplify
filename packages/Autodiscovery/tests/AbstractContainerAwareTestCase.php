<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\Autodiscovery\Tests\DependencyInjection\AudiscoveryTestingKernel;

abstract class AbstractContainerAwareTestCase extends TestCase
{
    use ContainerAwareTestCaseTrait;

    protected function getKernelClass(): string
    {
        return AudiscoveryTestingKernel::class;
    }
}
