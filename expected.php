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
        /** line in latte file: 3 */
        /* line 3 */
        $_tmp = $this->global->uiControl->getComponent("someName");
        if ($_tmp instanceof \Nette\Application\UI\Renderable) {
            $_tmp->redrawControl(\null, \false);
        }
        $_tmp->render('someValue');
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
