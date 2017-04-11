<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Application;

use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;
use Symplify\SymbioticController\Adapter\Nette\Routing\PresenterMapper;
use Symplify\SymbioticController\Adapter\Nette\Validator\PresenterGuardian;

final class PresenterFactory implements IPresenterFactory
{
    /**
     * @var string[]
     */
    private $presenterNameToPresenterClassMap = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var PresenterMapper
     */
    private $presenterMapper;

    /**
     * @var PresenterGuardian
     */
    private $presenterGuardian;

    public function __construct(
        Container $container,
        PresenterMapper $presenterMapper,
        PresenterGuardian $presenterGuardian
    ) {
        $this->container = $container;
        $this->presenterMapper = $presenterMapper;
        $this->presenterGuardian = $presenterGuardian;
    }

    /**
     * @param string $name
     * @return IPresenter|callable|object
     */
    public function createPresenter($name)
    {
        $presenterClass = $this->getPresenterClass($name);
        $presenter = $this->container->createInstance($presenterClass);

        if ($presenter instanceof Presenter) {
            $this->container->callInjects($presenter);
        }

        return $presenter;
    }

    /**
     * @param string $presenterName
     */
    public function getPresenterClass(&$presenterName): string
    {
        if (class_exists($presenterName)) {
            return $presenterName;
        }

        if (isset($this->presenterNameToPresenterClassMap[$presenterName])) {
            return $this->presenterNameToPresenterClassMap[$presenterName];
        }

        $this->presenterGuardian->ensurePresenterNameIsValid($presenterName);

        $presenterClass = $this->presenterMapper->detectPresenterClassFromPresenterName($presenterName);

        $this->ensurePresenterClassIsValid($presenterName, $presenterClass);

        return $this->presenterNameToPresenterClassMap[$presenterName] = $presenterClass;
    }

    /**
     * @param string[][]
     */
    public function setMapping(array $mapping): void
    {
        $this->presenterMapper->setMapping($mapping);
    }

    private function ensurePresenterClassIsValid(string $presenterName, string $presenterClass): void
    {
        $this->presenterGuardian->ensurePresenterClassExists($presenterName, $presenterClass);
        $this->presenterGuardian->ensurePresenterClassIsNotAbstract($presenterName, $presenterClass);
    }
}
