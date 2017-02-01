<?php declare(strict_types=1);

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\DI\ContainerFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\StandardNameToSniffsFactory;

final class StandardNameToSniffsFactoryTest extends TestCase
{
    /**
     * @var StandardNameToSniffsFactory
     */
    private $standardNameToSniffsFactory;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->standardNameToSniffsFactory = $container->getByType(StandardNameToSniffsFactory::class);
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->standardNameToSniffsFactory->isMatch('PSR1'));
        $this->assertFalse($this->standardNameToSniffsFactory->isMatch('nonexisting'));
    }

    public function testCreate()
    {
        $sniffs = $this->standardNameToSniffsFactory->create('PSR1');
        $this->assertCount(2, $sniffs);

        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniffs[2]);
        $this->assertInstanceOf(CamelCapsMethodNameSniff::class, $sniffs[4]);
    }
}
