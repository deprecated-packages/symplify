<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Wrong;

class SomeClass
{
    public function doctrine()
    {
        $this->containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => ['mappings' => $entityMappings],
        ]);
    }
}

?>
-----
<?php

namespace Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\Wrong;

class SomeClass
{
    public function doctrine()
    {
        $this->containerBuilder->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => $entityMappings
            ],
        ]);
    }
}

?>
