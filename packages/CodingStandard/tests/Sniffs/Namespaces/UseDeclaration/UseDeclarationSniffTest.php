<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\UseDeclaration;

use Symplify\CodingStandard\Sniffs\Namespaces\UseDeclarationSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class UseDeclarationSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(UseDeclarationSniff::class, __DIR__);
    }
}
