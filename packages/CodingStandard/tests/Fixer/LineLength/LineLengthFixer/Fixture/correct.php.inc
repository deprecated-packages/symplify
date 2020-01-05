<?php

declare(strict_types=1);

class SomeFixer
{
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Class should have suffix by parent class/interface', [
            new CodeSample(
                <<<CODE
<?php

class SomeClass extends Command
{
}
CODE
            ),
        ]);
    }
}
