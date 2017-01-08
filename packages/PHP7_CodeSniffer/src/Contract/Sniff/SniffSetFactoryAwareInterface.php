<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\Contract\Sniff;

use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;

interface SniffSetFactoryAwareInterface
{
    public function setSniffSetFactory(SniffSetFactory $sniffSetFactory);
}
