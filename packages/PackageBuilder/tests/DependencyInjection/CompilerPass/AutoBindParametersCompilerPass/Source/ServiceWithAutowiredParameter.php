<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass\Source;

final class ServiceWithAutowiredParameter
{
    /**
     * @var string
     */
    private $someParameter;

    /**
     * @var mixed[]
     */
    private $arrayParameter = [];

    /**
     * @param mixed $arrayParameter
     */
    public function __construct(string $someParameter, array $arrayParameter)
    {
        $this->someParameter = $someParameter;
        $this->arrayParameter = $arrayParameter;
    }

    public function getSomeParameter(): string
    {
        return $this->someParameter;
    }

    public function getArrayParameter(): array
    {
        return $this->arrayParameter;
    }
}
