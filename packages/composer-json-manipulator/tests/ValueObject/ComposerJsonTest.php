<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;

final class ComposerJsonTest extends TestCase
{
    public function testSorting(): void
    {
        $composerJson = new ComposerJson();
        $composerJson->addRequiredPackage('symfony/console', '^5.5');
        $composerJson->addRequiredPackage('nette/utils', '^3.2');

        $this->assertSame([
            'nette/utils' => '^3.2',
            'symfony/console' => '^5.5',
        ], $composerJson->getRequire());
    }

    public function testMovePackageToRequireDev(): void
    {
        $composerJson = new ComposerJson();
        $composerJson->addRequiredPackage('symfony/console', '^5.5');
        $composerJson->addRequiredDevPackage('symfony/http-kernel', '^5.5');

        $composerJson->movePackageToRequireDev('symfony/console');

        $this->assertSame([
            'symfony/console' => '^5.5',
            'symfony/http-kernel' => '^5.5',
        ], $composerJson->getRequireDev());
    }

    public function testReplacePacage(): void
    {
        $composerJson = new ComposerJson();
        $composerJson->addRequiredPackage('symfony/console', '^5.5');

        $composerJson->replacePackage('symfony/console', 'symfony/http-kernel', '^5.0');

        $this->assertSame([
            'symfony/http-kernel' => '^5.0',
        ], $composerJson->getRequire());
    }
}
