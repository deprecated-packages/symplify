<?php declare(strict_types=1);

namespace Zenify\ModularLatteFilters\Tests\DI;

use Nette\DI\Compiler;
use PHPUnit\Framework\TestCase;
use Zenify\ModularLatteFilters\DI\ModularLatteFiltersExtension;

final class MissingLatteDefinitionInExtensionTest extends TestCase
{

    /**
     * @expectedException \Zenify\ModularLatteFilters\Exception\DI\MissingLatteDefinitionException
     */
    public function testNoLatteDefinition()
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $extension->beforeCompile();
    }


    private function getExtension() : ModularLatteFiltersExtension
    {
        $extension = new ModularLatteFiltersExtension;
        $extension->setCompiler(new Compiler, 'compiler');
        return $extension;
    }
}
