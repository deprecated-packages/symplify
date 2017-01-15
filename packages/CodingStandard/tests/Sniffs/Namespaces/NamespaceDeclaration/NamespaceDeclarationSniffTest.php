<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\NamespaceDeclaration;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Namespaces\NamespaceDeclarationSniff;

final class NamespaceDeclarationSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(NamespaceDeclarationSniff::NAME, __DIR__);
    }
}
