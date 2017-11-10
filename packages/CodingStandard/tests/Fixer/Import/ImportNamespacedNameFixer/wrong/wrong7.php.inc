<?php

namespace SomeNamespace;

class SomeClass
{
    public function create(\Nette\Utils\Finder $someClass)
    {
        return new \Symfony\Finder\Finder;
    }
}
