<?php

namespace SomeNamespace;

use Symfony\Finder\Finder;
use Nette\Utils\Finder as NetteFinder;
class SomeClass
{
    public function create(NetteFinder $someClass)
    {
        return new Finder;
    }
}
