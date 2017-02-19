<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Namespaces\ClassNamesWithoutPreSlash;

use Symplify\CodingStandard\Sniffs\Namespaces\ClassNamesWithoutPreSlashSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class ClassNamesWithoutPreSlashSniffTest extends AbstractSniffTestCase
{
    public function test()
    {
        $this->runSniffTestForDirectory(ClassNamesWithoutPreSlashSniff::class, __DIR__);
    }
}
