<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Configuration;

use PharIo\Version\Version;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\MonorepoBuilder\Release\Version\VersionFactory;
use Symplify\MonorepoBuilder\ValueObject\Option;

final class VersionResolver
{
    /**
     * @var VersionFactory
     */
    private $versionFactory;

    public function __construct(VersionFactory $versionFactory)
    {
        $this->versionFactory = $versionFactory;
    }

    public function resolveVersion(InputInterface $input, string $stage): Version
    {
        /** @var string $versionArgument */
        $versionArgument = $input->getArgument(Option::VERSION);
        return $this->versionFactory->createValidVersion($versionArgument, $stage);
    }
}
