<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Contract\Routing;

interface PresenterMappingAwareInterface
{
    /**
     * @param string[][]
     */
    public function setMapping(array $mapping): void;
}
