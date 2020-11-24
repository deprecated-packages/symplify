<?php

declare(strict_types=1);

namespace Symplify\SnifferFixerToECSConverter\RobotLoader;

use Nette\Loaders\RobotLoader;

final class FixerClassProvider
{
    /**
     * @var string[]
     */
    private $fixerClasses = [];

    /**
     * @return string[]
     */
    public function provide(): array
    {
        if ($this->fixerClasses !== []) {
            return $this->fixerClasses;
        }

        $robotLoader = new RobotLoader();
        $robotLoader->addDirectory(__DIR__ . '/../../../../vendor/friendsofphp/php-cs-fixer/src');

        $robotLoader->acceptFiles = ['*Fixer.php'];
        $robotLoader->rebuild();

        $this->fixerClasses = array_keys($robotLoader->getIndexedClasses());

        return $this->fixerClasses;
    }
}
