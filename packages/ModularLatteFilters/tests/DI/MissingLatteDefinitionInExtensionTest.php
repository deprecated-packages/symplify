<?php declare(strict_types=1);

namespace Symplify\ModularLatteFilters\Tests\DI;

use Nette\DI\Compiler;
use PHPUnit\Framework\TestCase;
use Symplify\ModularLatteFilters\DI\ModularLatteFiltersExtension;
use Symplify\ModularLatteFilters\Exception\DI\MissingLatteDefinitionException;

final class MissingLatteDefinitionInExtensionTest extends TestCase
{
    public function testNoLatteDefinition(): void
    {
        $extension = $this->getExtension();
        $extension->loadConfiguration();

        $this->expectException(MissingLatteDefinitionException::class);
        $extension->beforeCompile();
    }

    private function getExtension(): ModularLatteFiltersExtension
    {
        $extension = new ModularLatteFiltersExtension;
        $extension->setCompiler(new Compiler, 'compiler');

        return $extension;
    }
}
