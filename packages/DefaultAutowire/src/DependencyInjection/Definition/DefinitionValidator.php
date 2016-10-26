<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\DefaultAutowire\DependencyInjection\Definition;

use Symfony\Component\DependencyInjection\Definition;

final class DefinitionValidator
{
    public function validate(Definition $definition) : bool
    {
        if (! $definition->isPublic() || $definition->isAbstract()) {
            return false;
        }

        if (null === $definition->getClass() || ! class_exists($definition->getClass())) {
            return false;
        }

        return true;
    }
}
