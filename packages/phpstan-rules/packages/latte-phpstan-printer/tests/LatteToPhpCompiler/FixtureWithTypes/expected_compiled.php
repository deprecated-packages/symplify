<?php

use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    public function main() : array
    {
        \extract($this->params);
        /** @var string $someName */
        echo '%s/packages/latte-phpstan-printer/tests/LatteToPhpCompiler/FixtureWithTypes/input_file.latte';
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        /** @var string $someName */
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
