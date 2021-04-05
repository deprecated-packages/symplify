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

        if (defined('SYMPLIFY_MONOREPO')) {
            $robotLoader->addDirectory(__DIR__ . '/../../../../vendor/friendsofphp/php-cs-fixer/src');
        } else {
            $robotLoader->addDirectory(__DIR__ . '/../../../../friendsofphp/php-cs-fixer/src');
        }

        $robotLoader->acceptFiles = ['*Fixer.php'];
        $robotLoader->rebuild();

        $fixedClasses = [];
        foreach (array_keys($robotLoader->getIndexedClasses()) as $class) {
            if (! is_string($class)) {
                continue;
            }

            $fixedClasses[] = $class;
        }

        $this->fixerClasses = $fixedClasses;

        return $fixedClasses;
    }
}
