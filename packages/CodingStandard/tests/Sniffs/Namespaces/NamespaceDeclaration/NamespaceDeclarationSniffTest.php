<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\NamespaceDeclaration;

use Symplify\CodingStandard\Sniffs\Namespaces\NamespaceDeclarationSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class NamespaceDeclarationSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(NamespaceDeclarationSniff::class, __DIR__);
    }
}
