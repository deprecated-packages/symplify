<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\UseDeclaration;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use SymplifyCodingStandard\Sniffs\Namespaces\UseDeclarationSniff;

final class UseDeclarationSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(UseDeclarationSniff::NAME, __DIR__);
    }
}
