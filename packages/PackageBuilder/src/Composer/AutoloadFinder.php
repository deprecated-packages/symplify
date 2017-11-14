<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Composer;

final class AutoloadFinder
{
    /**
     * @param string[] $directories
     */
    public static function findNearDirectories(array $directories): ?string
    {
        foreach ($directories as $directory) {
            $directory = rtrim($directory);

            if (file_exists($directory . '/autoload.php')) {
                return $directory . '/autoload.php';
            }

            for ($i = 0; $i < 5; ++$i) {
                $autoload = $directory . str_repeat('/..', $i) . '/vendor/autoload.php';
                if (file_exists($autoload)) {
                    return realpath($autoload);
                }
            }
        }

        return null;
    }
}
