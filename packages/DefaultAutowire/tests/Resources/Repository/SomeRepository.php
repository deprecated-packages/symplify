<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Resources\Repository;

use Symplify\DefaultAutowire\Tests\Source\FakeEntityManager;

final class SomeRepository
{
    /**
     * @var FakeEntityManager
     */
    private $entityManager;

    public function __construct(FakeEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getEntityManager(): FakeEntityManager
    {
        return $this->entityManager;
    }
}
