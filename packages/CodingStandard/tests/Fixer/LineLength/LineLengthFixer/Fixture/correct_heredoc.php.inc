<?php

declare(strict_types=1);

namespace Rector\Php\Rector\FuncCall;

final class SensitiveDefineRector
{
    public function getNowDoc()
    {
        return new RectorDefinition(
            [new CodeSample(
                <<<'PHP'
define('FOO', 42, true); 
PHP
            )]
        );
    }

    public function getHereDoc()
    {
        return new RectorDefinition(
            [new CodeSample(
                <<<PHP
define('FOO', 42, true); 
PHP
            )]
        );
    }
}

?>
-----
<?php

declare(strict_types=1);

namespace Rector\Php\Rector\FuncCall;

final class SensitiveDefineRector
{
    public function getNowDoc()
    {
        return new RectorDefinition([new CodeSample(<<<'PHP'
define('FOO', 42, true); 
PHP
            )]);
    }

    public function getHereDoc()
    {
        return new RectorDefinition([new CodeSample(<<<PHP
define('FOO', 42, true); 
PHP
            )]);
    }
}

?>
