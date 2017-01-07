<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class ConfigurationResolverTest extends TestCase
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    protected function setUp()
    {
        $this->configurationResolver = Instantiator::createConfigurationResolver();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\StandardNotFoundException
     */
    public function testNonExistingStandard()
    {
        $this->configurationResolver->resolve('standards', ['fake']);
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\InvalidSniffCodeException
     */
    public function testInvalidSniffCode()
    {
        $this->configurationResolver->resolve('sniffs', ['invalid.code']);
    }

    public function testResolve()
    {
        $this->assertSame(
            [__DIR__],
            $this->configurationResolver->resolve('source', [__DIR__])
        );

        $this->assertSame(
            ['PSR2'],
            $this->configurationResolver->resolve('standards', ['PSR2'])
        );

        $this->assertSame(
            ['PSR1', 'PSR2'],
            $this->configurationResolver->resolve('standards', ['PSR1,PSR2'])
        );

        $this->assertSame(
            ['PEAR.Commenting.ClassComment'],
            $this->configurationResolver->resolve('sniffs', ['PEAR.Commenting.ClassComment'])
        );

        $this->assertSame(
            ['PEAR.Commenting.ClassComment', 'SomeOther.Commenting.ClassComment'],
            $this->configurationResolver->resolve(
                'sniffs',
                ['PEAR.Commenting.ClassComment', 'SomeOther.Commenting.ClassComment']
            )
        );
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\MissingOptionResolverException
     */
    public function testResolveNonExistingOption()
    {
        $this->configurationResolver->resolve('random', []);
    }
}
