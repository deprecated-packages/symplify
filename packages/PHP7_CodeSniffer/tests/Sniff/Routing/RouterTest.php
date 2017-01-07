<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Routing;

use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowSpaceIndentSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Routing\Router;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    protected function setUp()
    {
        $this->router = new Router(Instantiator::createSniffFinder());
    }

    public function testGetClassFromSniffName()
    {
        $this->assertSame(
            DisallowSpaceIndentSniff::class,
            $this->router->getClassFromSniffCode('Generic.WhiteSpace.DisallowSpaceIndent')
        );

        $this->assertSame(
            ClassDeclarationSniff::class,
            $this->router->getClassFromSniffCode('PSR2.Classes.ClassDeclaration')
        );
    }

    public function testGetClassFromSniffNameRandom()
    {
        $this->assertSame('', $this->router->getClassFromSniffCode('Non.Existing.Sniff'));
    }
}
