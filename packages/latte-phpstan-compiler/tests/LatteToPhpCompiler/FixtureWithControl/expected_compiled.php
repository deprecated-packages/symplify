<?php

declare (strict_types=1);
use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    public function main() : array
    {
        \extract($this->params);
        echo '<h1>Some component</h1>

';
        /** @var \Symplify\PHPStanRules\LattePHPStanPrinter\Tests\LatteToPhpCompiler\Source\SomeNameControl $someNameControl */
        /** line in latte file: 3 */
        /* line 3 */
        $someNameControl = $this->global->uiControl->getComponent("someName");
        if ($someNameControl instanceof \Nette\Application\UI\Renderable) {
            $someNameControl->redrawControl(\null, \false);
        }
        $someNameControl->render('someValue');
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
