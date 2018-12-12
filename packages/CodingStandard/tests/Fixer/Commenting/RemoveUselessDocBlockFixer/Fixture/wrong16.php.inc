<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param UrlGeneratorInterface $generator  The URL Generator
     * @param Environment           $twig       The Twig Environment
     */
    public function load(UrlGeneratorInterface $generator, Environment $twig)
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function load(UrlGeneratorInterface $generator, Environment $twig)
    {
    }
}

?>
