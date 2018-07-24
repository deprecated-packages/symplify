<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass\Source;

final class ServiceWithAutowiredParameter
{
    /**
     * @var string
     */
    private $someParameter;

    public function __construct(string $someParameter)
    {
        $this->someParameter = $someParameter;
    }

    public function getSomeParameter(): string
    {
        return $this->someParameter;
    }
}
