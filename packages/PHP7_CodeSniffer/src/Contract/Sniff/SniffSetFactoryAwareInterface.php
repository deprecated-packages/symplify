<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Contract\Sniff;

use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;

interface SniffSetFactoryAwareInterface
{
    public function setSniffSetFactory(SniffSetFactory $sniffSetFactory);
}
