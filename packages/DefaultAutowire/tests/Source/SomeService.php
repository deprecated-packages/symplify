<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\Tests\Source;

use Symplify\DefaultAutowire\Tests\Resources\Repository\SomeRepository;

final class SomeService
{
    /**
     * @var SomeRepository
     */
    private $someRepository;

    public function __construct(SomeRepository $someRepository)
    {
        $this->someRepository = $someRepository;
    }
}
