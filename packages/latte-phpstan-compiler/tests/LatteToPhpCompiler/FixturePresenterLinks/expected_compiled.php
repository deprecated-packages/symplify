<?php

declare (strict_types=1);
use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    public function main() : array
    {
        \extract($this->params);
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $actualClass */
        echo '<a href="';
        /** line in latte file: 1 */
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $fooPresenter */
        $fooPresenter->renderDefault(10);
        echo '">link</a>
<a href="';
        /** line in latte file: 2 */
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $fooPresenter */
        $fooPresenter->renderDefault(20, ['c' => 'd']);
        echo '">n:href</a>
<a href="';
        /** line in latte file: 3 */
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $fooPresenter */
        $fooPresenter->actionGrid(10, ['c' => 'd']);
        $fooPresenter->renderGrid(10, ['c' => 'd']);
        echo '">Two methods absolute link</a>
<a href="';
        /** line in latte file: 4 */
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $fooPresenter */
        $fooPresenter->actionGrid(20);
        $fooPresenter->renderGrid(20);
        echo '">Two methods n:href</a>
<a href="';
        /** line in latte file: 5 */
        $actualClass->handleDoSomething('a');
        echo '">signal</a>
';
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\FooPresenter $actualClass */
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
