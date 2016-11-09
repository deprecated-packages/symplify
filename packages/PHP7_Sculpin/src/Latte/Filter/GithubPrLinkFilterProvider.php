<?php

declare(strict_types=1);

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_Sculpin\Latte\Filter;

use Symplify\PHP7_Sculpin\Configuration\Configuration;
use Symplify\PHP7_Sculpin\Renderable\File\AbstractFile;
use Zenify\ModularLatteFilters\Contract\DI\LatteFiltersProviderInterface;

final class GithubPrLinkFilterProvider implements LatteFiltersProviderInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return callable[]
     */
    public function getFilters() : array
    {
        return [
            'githubEditPostUrl' => function (AbstractFile $file) {
                return 'https://github.com/'
                    . $this->configuration->getGithubRepositorySlug()
                    . '/edit/master/source/'
                    . $file->getRelativeSource();
            }
        ];
    }
}
