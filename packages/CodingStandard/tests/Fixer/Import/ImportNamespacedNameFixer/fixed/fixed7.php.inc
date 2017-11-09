<?php

namespace SomeNamespace;

use Nette\Utils\Finder as NetteFinder;
use Symfony\Finder\Finder;
class SomeClass
{
    public function create(NetteFinder $someClass)
    {
        return new Finder;
    }
}
