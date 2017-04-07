<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\HttpKernel\Controller;

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
     * @param string[] $dirs
     * @return string[]
     */
    public function findControllersInDirs(array $dirs): array
    {
        $robot = new RobotLoader;
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

    /**
     * @param string[] $controllerClasses
     * @return string[]
     */
    private function prepareServiceKeys(array $controllerClasses): array
    {
        $controllerClassesWithKeys = [];
        foreach ($controllerClasses as $key => $controllerClass) {
            $key = strtr(strtolower($controllerClass), ['\\' => '.']);
            $controllerClassesWithKeys[$key] = $controllerClass;
        }

        return $controllerClassesWithKeys;
    }
}
