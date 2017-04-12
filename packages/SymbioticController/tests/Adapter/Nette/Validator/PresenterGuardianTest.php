<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Tests\Adapter\Nette\Validator;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\SymbioticController\Adapter\Nette\Validator\PresenterGuardian;
use Symplify\SymbioticController\Tests\Adapter\Nette\Validator\PresenterGuardianSource\AbstractClass;

final class PresenterGuardianTest extends TestCase
{
    /**
     * @var PresenterGuardian
     */
    private $presenterGuardian;

    protected function setUp(): void
    {
        $this->presenterGuardian = new PresenterGuardian;
    }

    /**
     * @expectedException \Nette\Application\InvalidPresenterException
     */
    public function testEnsurePresenterClassExistsFails(): void
    {
        $this->presenterGuardian->ensurePresenterClassExists('missingName', 'missingClass');
    }

    public function testEnsurePresenterClassExistsSuccess(): void
    {
        $this->presenterGuardian->ensurePresenterClassExists('missingName', stdClass::class);
        $this->assertTrue(true);
    }

    /**
     * @dataProvider providePresenterNames()
     */
    public function testEnsurePresenterNameIsValid(string $presenterName): void
    {
        $this->presenterGuardian->ensurePresenterNameIsValid($presenterName);

        $this->assertTrue(true);
    }

    /**
     * @return string[]
     */
    public function providePresenterNames(): array
    {
        return [
            [stdClass::class],
            ['validName'],
            ['validModule:validName'],
            [':validModule:validName'],
        ];
    }

    /**
     * @expectedException \Nette\Application\InvalidPresenterException
     */
    public function testEnsurePresenterClassIsNotAbstract(): void
    {
        $this->presenterGuardian->ensurePresenterClassIsNotAbstract('someName', AbstractClass::class);
    }
}
