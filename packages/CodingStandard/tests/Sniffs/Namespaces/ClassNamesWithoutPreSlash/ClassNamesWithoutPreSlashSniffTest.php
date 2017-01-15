<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\ClassNamesWithoutPreSlash;

use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;
use Symplify\CodingStandard\Sniffs\Namespaces\ClassNamesWithoutPreSlashSniff;

final class ClassNamesWithoutPreSlashSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(ClassNamesWithoutPreSlashSniff::NAME, __DIR__);
    }
}
