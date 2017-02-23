<?php declare(strict_types=1);

namespace Symplify\ModularRouting\Tests\PHPUnit\Listener;

use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\TestSuite;

final class ClearLogAndCacheTestListener extends BaseTestListener
{
    public function endTestSuite(TestSuite $testSuite): void
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
    private function getTempAndLogDirectories(): array
    {
        $finder = Finder::findDirectories('cache', 'logs')->from(__DIR__ . '/../..');
        return iterator_to_array($finder->getIterator());
    }
}
