<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\NodeFactory;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use function str_contains;
use function str_starts_with;

/**
 * @see https://github.com/symfony/symfony/pull/18626/files
 *
 * @see \Symplify\PhpConfigPrinter\Tests\NodeFactory\ConstantNodeFactoryTest
 */
final class ConstantNodeFactory
{
    /**
     * @see https://regex101.com/r/xrllDg/1
     * @var string
     */
    private const CLASS_CONST_FETCH_REGEX = '#(.*?)::[A-Za-z_]#';
    
    public function createClassConstantIfValue(string $value, bool $checkExistence = true): ?ClassConstFetch {
        $match = Strings::match($value, self::CLASS_CONST_FETCH_REGEX);
        if ($match !== null) {
            [$class, $constant] = explode('::', $value);
            if (!$checkExistence) {
                return new ClassConstFetch(new FullyQualified($class), $constant);
            }

            if (class_exists($class)) {
                return new ClassConstFetch(new FullyQualified($class), $constant);
            }
        }

        return null;
    }

    public function createConstant(string $value): ConstFetch|ClassConstFetch
    {
        $classConstFetch = $this->createClassConstantIfValue($value, false);
        if ($classConstFetch !== null) {
            return $classConstFetch;
        }

        return new ConstFetch(new Name($value));
    }
}
