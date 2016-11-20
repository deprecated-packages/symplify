<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Controller\Session;

use Symfony\Component\HttpFoundation\Session\Session;

trait ControllerFlashTrait
{
    /**
     * @var Session
     */
    private $session;

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    protected function addFlash(string $type, string $message)
    {
        $this->session->getFlashBag()
            ->add($type, $message);
    }
}
