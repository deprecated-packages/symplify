<?php declare(strict_types=1);

namespace Symplify\ModularRouting\Tests\PHPUnit\Listener;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PHPUnit_Framework_BaseTestListener;
use PHPUnit_Framework_TestSuite;

final class ClearLogAndCacheTestListener extends PHPUnit_Framework_BaseTestListener
{
    public function endTestSuite(PHPUnit_Framework_TestSuite $testSuite)
    {
        if ($testSuite->getName()) { // skip for tests, run only for whole Test Suite
            return;
        }

        foreach ($this->getTempAndLogDirectories() as $path => $info) {
            FileSystem::delete($path);
        }
    }

    /**
     * @return string[]
     */
    private function getTempAndLogDirectories() : array
    {
        $finder = Finder::findDirectories('cache', 'logs')->from(__DIR__ . '/../..');
        return iterator_to_array($finder->getIterator());
    }
}
