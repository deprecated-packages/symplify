<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes;

use Symplify\CodingStandard\Sniffs\Classes\EqualInterfaceImplementationSniff;
use Symplify\CodingStandard\Tests\Sniffs\AbstractSniffTestCase;

final class EqualInterfaceImplementationSniffTest extends AbstractSniffTestCase
{
    public function test(): void
    {
        $this->runSniffTestForDirectory(EqualInterfaceImplementationSniff::class, __DIR__);
    }
}
