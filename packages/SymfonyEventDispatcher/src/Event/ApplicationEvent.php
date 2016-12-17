<?php

declare(strict_types=1);

/**
 * This file is part of Symnedi.
 * Copyright (c) 2014 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\SymfonyEventDispatcher\Event;

use Nette\Application\Application;
use Symfony\Component\EventDispatcher\Event;

final class ApplicationEvent extends Event
{
    /**
     * @var Application
     */
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    public function getApplication() : Application
    {
        return $this->application;
    }
}
