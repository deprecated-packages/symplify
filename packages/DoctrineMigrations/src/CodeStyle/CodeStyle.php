<?php declare(strict_types=1);

namespace Zenify\DoctrineMigrations\CodeStyle;

final class CodeStyle
{
    /**
     * @var string
     */
    public const INDENTATION_TABS = 'tabs';

    /**
     * @var string
     */
    public const INDENTATION_SPACES = 'spaces';

    /**
     * @var string
     */
    private $indentationStandard;

    public function __construct(string $indentationStandard)
    {
        $this->indentationStandard = $indentationStandard;
    }

    public function applyForFile(string $file) : void
    {
        if ($this->indentationStandard === self::INDENTATION_TABS) {
            $this->convertSpacesToTabsForFile($file);
        }
    }

    private function convertSpacesToTabsForFile(string $file) : void
    {
        $code = file_get_contents($file);
        $code = preg_replace('/ {4}/', "\t", $code);
        file_put_contents($file, $code);
    }
}
