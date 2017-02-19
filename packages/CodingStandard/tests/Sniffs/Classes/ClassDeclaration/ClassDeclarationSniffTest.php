<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes\ClassDeclaration;

use Symplify\CodingStandard\Sniffs\Classes\ClassDeclarationSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ClassDeclarationSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(ClassDeclarationSniff::class, __DIR__);
    }
}
