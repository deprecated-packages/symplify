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

    public function testEnsurePresenterNameIsValid(): void
    {
        $this->presenterGuardian->ensurePresenterNameIsValid(stdClass::class);
        $this->presenterGuardian->ensurePresenterNameIsValid('validName');
        $this->presenterGuardian->ensurePresenterNameIsValid('validModule:validName');
        $this->presenterGuardian->ensurePresenterNameIsValid(':validModule:validName');

        $this->assertTrue(true);
    }

    /**
     * @expectedException \Nette\Application\InvalidPresenterException
     */
    public function testEnsurePresenterClassIsNotAbstract(): void
    {
        $this->presenterGuardian->ensurePresenterClassIsNotAbstract('someName', AbstractClass::class);
    }
}
