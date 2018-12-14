<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param array[] $tokens
     * @param Fixer $fixer
     * @param ErrorCollector $errorCollector
     */
    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorCollector $errorCollector,
        bool $isFixer,
        CurrentSniffProvider $currentSniffProvider,
        Skipper $skipper
    ) {
    }
}

?>
-----
<?php
namespace SomeNamespace;

class SomeClass
{
    /**
     * @param array[] $tokens
     */
    public function __construct(
        string $path,
        array $tokens,
        Fixer $fixer,
        ErrorCollector $errorCollector,
        bool $isFixer,
        CurrentSniffProvider $currentSniffProvider,
        Skipper $skipper
    ) {
    }
}

?>
