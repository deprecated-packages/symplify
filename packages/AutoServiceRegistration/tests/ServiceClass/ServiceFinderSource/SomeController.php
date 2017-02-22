<?php declare(strict_types=1);

namespace Symplify\AutoServiceRegistration\Tests\ServiceClass\ServiceFinderSource;

class SomeController
{
    /**
     * @var SomeRepository
     */
    private $someRepository;

    public function __construct(SomeRepository $someRepository)
    {
        $this->someRepository = $someRepository;
    }

    public function getSomeRepository(): SomeRepository
    {
        return $this->someRepository;
    }
}
