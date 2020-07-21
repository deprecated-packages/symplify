<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\SetConfigResolver\Config\SetsParameterResolver;
use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see \Symplify\SetConfigResolver\Tests\ConfigResolver\SetAwareConfigResolverTest
 */
final class SetAwareConfigResolver extends AbstractConfigResolver
{
    /**
     * @var SetsParameterResolver
     */
    private $setsParameterResolver;

    /**
     * @var SetResolver
     */
    private $setResolver;

    public function __construct(SetProviderInterface $setProvider)
    {
        $this->setResolver = new SetResolver($setProvider);
        $this->setsParameterResolver = new SetsParameterResolver($this->setResolver);

        parent::__construct();
    }

    /**
     * @param SmartFileInfo[] $fileInfos
     * @return SmartFileInfo[]
     */
    public function resolveFromParameterSetsFromConfigFiles(array $fileInfos): array
    {
        return $this->setsParameterResolver->resolveFromFileInfos($fileInfos);
    }

    public function resolveSetFromInput(InputInterface $input): ?SmartFileInfo
    {
        return $this->setResolver->detectFromInput($input);
    }
}
