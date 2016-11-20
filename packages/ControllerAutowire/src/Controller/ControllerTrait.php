<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\ControllerAutowire\Controller;

use Symplify\ControllerAutowire\Controller\Doctrine\ControllerDoctrineTrait;
use Symplify\ControllerAutowire\Controller\Form\ControllerFormTrait;
use Symplify\ControllerAutowire\Controller\HttpKernel\ControllerHttpKernelTrait;
use Symplify\ControllerAutowire\Controller\Routing\ControllerRoutingTrait;
use Symplify\ControllerAutowire\Controller\Security\ControllerSecurityTrait;
use Symplify\ControllerAutowire\Controller\Serializer\ControllerSerializerTrait;
use Symplify\ControllerAutowire\Controller\Session\ControllerFlashTrait;
use Symplify\ControllerAutowire\Controller\Templating\ControllerRenderTrait;

trait ControllerTrait
{
    use ControllerFormTrait;
    use ControllerRoutingTrait;
    use ControllerHttpKernelTrait;
    use ControllerSecurityTrait;
    use ControllerSerializerTrait;
    use ControllerRenderTrait;
    use ControllerDoctrineTrait;
    use ControllerFlashTrait;
}
