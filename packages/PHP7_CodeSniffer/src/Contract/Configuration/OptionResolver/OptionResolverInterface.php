<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver;

interface OptionResolverInterface
{
    public function getName() : string;

    public function resolve(array $value) : array;
}
