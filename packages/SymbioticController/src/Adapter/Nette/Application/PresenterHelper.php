<?php declare(strict_types=1);

namespace Symplify\SymbioticController\Adapter\Nette\Application;

use Nette\Application\UI\Presenter;

final class PresenterHelper extends Presenter
{
    public function __construct()
    {
        parent::__construct();
        $this->saveGlobalState();
    }

    /**
     * @param string $destination
     * @param mixed[] $args
     */
    public function link($destination, $args = []): string
    {
        $this->invalidLinkMode = self::INVALID_LINK_EXCEPTION;
        return parent::link($destination, $args);
    }
}
