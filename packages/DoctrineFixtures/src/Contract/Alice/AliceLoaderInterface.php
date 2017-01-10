<?php declare(strict_types=1);

namespace Zenify\DoctrineFixtures\Contract\Alice;

interface AliceLoaderInterface
{

    /**
     * Loads fixtures from one or more files/folders.
     *
     * @param string|array $sources
     * @return object[]
     */
    public function load($sources) : array;
}
