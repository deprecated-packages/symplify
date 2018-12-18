<?php declare(strict_types=1);

namespace Symplify\Autodiscovery\Contract;

interface AutodiscovererInterface
{
    public function autodiscover(): void;
}
