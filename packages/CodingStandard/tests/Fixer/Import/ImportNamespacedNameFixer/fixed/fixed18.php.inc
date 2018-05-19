<?php

namespace Shopsys\FrameworkBundle\Model\Administrator;

use App\Process\UserClient\Edit;
use App\Process\UserSuperAdmin\Edit as AppEdit;
class AdministratorGridFacade
{
    /**
     * @var Edit|AppEdit
     */
    protected $edit;
}
