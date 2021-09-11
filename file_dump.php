<?php

use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends Latte\Runtime\Template
{
    public function main() : array
    {
        extract($this->params);
        /** @var Symplify\PHPStanRules\Nette\Tests\Rules\NoLatteMissingMethodCallRule\Source\SomeTypeWithMethods $someType */
        echo LR\Filters::escapeHtmlText($someType->missingMethod());
        echo "\n";
        return get_defined_vars();
    }
    public function prepare() : void
    {
        extract($this->params);
        Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}