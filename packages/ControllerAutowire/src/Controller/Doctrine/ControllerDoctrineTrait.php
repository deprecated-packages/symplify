<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Controller\Doctrine;

use Doctrine\Common\Persistence\ManagerRegistry;

trait ControllerDoctrineTrait
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function setDoctrine(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function getDoctrine() : ManagerRegistry
    {
        return $this->doctrine;
    }
}
