<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\HttpKernel\Controller;

use Nette\Caching\Storages\DevNullStorage;
use Nette\Loaders\RobotLoader;
use Symplify\ControllerAutowire\Contract\HttpKernel\ControllerFinderInterface;

final class ControllerFinder implements ControllerFinderInterface
{
    /**
     * @var string
     */
    private $namePart;

    public function __construct(string $namePart = 'Controller')
    {
        $this->namePart = $namePart;
    }

    /**
<<<<<<< 5cd59f9784c144a7a320f3072d016ce771655456
     * @return string[]
=======
     * @param array $dirs
     * @return array
>>>>>>> drop inheritdoc, no info value
     */
    public function findControllersInDirs(array $dirs) : array
    {
        $robot = new RobotLoader;
        $robot->setCacheStorage(new DevNullStorage);
        foreach ($dirs as $dir) {
            $robot->addDirectory($dir);
        }
        $robot->ignoreDirs .= ', Tests';
        $robot->acceptFiles = '*' . $this->namePart . '.php';
        $robot->rebuild();

        $controllerClasses = array_keys($robot->getIndexedClasses());
        sort($controllerClasses);

        return $this->prepareServiceKeys($controllerClasses);
    }

    private function prepareServiceKeys(array $controllerClasses) : array
    {
        $controllerClassesWithKeys = [];
        foreach ($controllerClasses as $key => $controllerClass) {
            $key = strtr(strtolower($controllerClass), ['\\' => '.']);
            $controllerClassesWithKeys[$key] = $controllerClass;
        }

        return $controllerClassesWithKeys;
    }
}
