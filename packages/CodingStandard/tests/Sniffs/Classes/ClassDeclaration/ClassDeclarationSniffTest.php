<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes\ClassDeclaration;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Classes\ClassDeclarationSniff;

final class ClassDeclarationSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(ClassDeclarationSniff::NAME, __DIR__);
    }
}
