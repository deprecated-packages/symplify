<?php

declare(strict_types=1);

namespace Symplify\NetteAdapterForSymfonyBundles\Tests\ContainerSource;

final class ParameterStorage
{
    /**
     * @var string
     */
    private $parameter;

    /**
     * @var array
     */
    private $groupOfParameters = [];

    public function __construct(string $parameter, array $groupOfParameters)
    {
        $this->parameter = $parameter;
        $this->groupOfParameters = $groupOfParameters;
    }

    public function getParameter() : string
    {
        return $this->parameter;
    }

    public function getGroupOfParameters() : array
    {
        return $this->groupOfParameters;
    }
}
