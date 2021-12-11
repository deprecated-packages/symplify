<?php

declare (strict_types=1);
use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    public function main() : array
    {
        \extract($this->params);
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $presenter */
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $control */
        /** line in latte file: 1 */
        echo \Latte\Runtime\Filters::escapeHtmlText($presenter->foo);
        echo "\n";
        /** line in latte file: 2 */
        echo \Latte\Runtime\Filters::escapeHtmlText($control->foo);
        echo "\n";
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $presenter */
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $control */
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
