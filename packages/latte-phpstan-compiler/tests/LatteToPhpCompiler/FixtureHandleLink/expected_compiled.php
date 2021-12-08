<?php

declare (strict_types=1);
use Latte\Runtime as LR;
/** DummyTemplateClass */
final class DummyTemplateClass extends \Latte\Runtime\Template
{
    public function main() : array
    {
        \extract($this->params);
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\SomeNameControl $actualClass */
        echo '<a href="';
        /** line in latte file: 1 */
        $actualClass->handleDoSomething('a');
        echo '">link</a>
<a href="';
        /** line in latte file: 2 */
        $actualClass->handleDoSomething('b', ['c' => 'd']);
        echo '">n:href</a>
<a href="';
        /** line in latte file: 3 */
        $actualClass->handleWithoutParameters();
        echo '">link no params</a>
<a href="';
        /** line in latte file: 4 */
        $actualClass->handleWithoutParameters();
        echo '">n:href no params</a>
';
        return \get_defined_vars();
    }
    public function prepare() : void
    {
        \extract($this->params);
        /** @var Symplify\LattePHPStanCompiler\Tests\LatteToPhpCompiler\Source\SomeNameControl $actualClass */
        \Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
    }
}
