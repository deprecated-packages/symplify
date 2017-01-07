<?php

declare(strict_types=1);

namespace Symplify\SymfonySecurity\Contract\DI;

interface ModularFirewallInterface
{
    public function getFirewallName() : string;
}
