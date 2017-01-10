<?php declare(strict_types=1);

namespace Symplify\SymfonySecurity\Tests\Adapter\Nette\DI;

use Nette\DI\Compiler;
use Nette\DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symplify\SymfonySecurity\Adapter\Nette\DI\SymfonySecurityExtension;

abstract class AbstractSecurityExtensionTestCase extends TestCase
{
    protected function getExtension() : SymfonySecurityExtension
    {
        $extension = new SymfonySecurityExtension;
        $extension->setCompiler(new Compiler(new ContainerBuilder), 'compiler');

        return $extension;
    }
}
