<?php

declare(strict_types = 1);

namespace Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver;

interface OptionResolverInterface
{
    public function getName() : string;

    public function resolve(array $value) : array;
}
