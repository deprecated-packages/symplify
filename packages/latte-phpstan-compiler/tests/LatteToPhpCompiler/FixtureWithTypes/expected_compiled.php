<?php

declare (strict_types=1);
use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    protected const BLOCKS = ['snippet' => ['name' => 'blockName']];
    public function main() : array
    {
        \extract($this->params);
        /** @var string $someName */
        echo '<div id="';
        echo \htmlspecialchars($this->global->snippetDriver->getHtmlId('name'));
        echo '">';
        /** line in latte file: 1 */
        $this->renderBlock('name', [], \null, 'snippet');
        echo '</div>
';
        return \get_defined_vars();
    }
    /** {snippet name} on line 1 */
    public function blockName(array $ʟ_args) : void
    {
        \extract($this->params);
        /** @var string $someName */
        \extract($ʟ_args);
        unset($ʟ_args);
        $this->global->snippetDriver->enter("name", 'static');
        try {
            echo '    ';
            /** line in latte file: 2 */
            echo \Latte\Runtime\Filters::escapeHtmlText($value);
            echo "\n";
        } finally {
            $this->global->snippetDriver->leave();
        }
    }
}
