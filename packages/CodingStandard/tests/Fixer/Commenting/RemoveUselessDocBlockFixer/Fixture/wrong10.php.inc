<?php

namespace SomeNamespace;

class SomeClass
{
    /**
     * @param ConvertEvent $convertEvent Convert Event
     */
    public function beforeConvert(ConvertEvent $convertEvent)
    {
    }

    /**
     * @param string $name Name
     *
     * @return ConverterInterface
     */
    public function converter(string $name): ConverterInterface
    {
    }
}

?>
-----
<?php

namespace SomeNamespace;

class SomeClass
{
    public function beforeConvert(ConvertEvent $convertEvent)
    {
    }

    public function converter(string $name): ConverterInterface
    {
    }
}

?>
