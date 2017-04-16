<?php declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Sniffs\Classes\EqualInterfaceImplementation\Source;

/**
 * This class is duplicated due to autoloading.
 * Only that way we can get reflection from SomeInterface and its methods.
 */
class SomeClass implements SomeInterface
{
    public function resolve()
    {

    }
}
