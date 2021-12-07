<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\Nette;

use Nette\Application\PresenterFactory;

final class PresenterFactoryFaker
{
    /**
     * @param array<string, string> $mapping
     */
    public function __construct(
        private array $mapping
    ) {
    }

    public function getPresenterFactory(): PresenterFactory
    {
        $presenterFactory = new PresenterFactory();
        $presenterFactory->setMapping($this->mapping);
        return $presenterFactory;
    }
}
