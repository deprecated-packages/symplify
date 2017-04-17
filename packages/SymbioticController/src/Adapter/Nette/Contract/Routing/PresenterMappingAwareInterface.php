<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Routing\Contract;

interface PresenterMappingAwareInterface
{
    /**
     * @param string[][]
     */

    public function setMapping(array $mapping): void;
}
