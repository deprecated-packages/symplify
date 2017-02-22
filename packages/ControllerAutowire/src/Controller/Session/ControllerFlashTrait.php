<?php declare(strict_types=1);

namespace Symplify\ControllerAutowire\Controller\Session;

use Symfony\Component\HttpFoundation\Session\Session;

trait ControllerFlashTrait
{
    /**
     * @var Session
     */
    private $session;

    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

    protected function addFlash(string $type, string $message): void
    {
        $this->session->getFlashBag()
            ->add($type, $message);
    }
}
