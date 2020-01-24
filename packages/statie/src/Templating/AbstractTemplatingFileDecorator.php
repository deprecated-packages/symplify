<?php

declare(strict_types=1);

namespace Symplify\Statie\Templating;

use Symplify\Statie\Configuration\StatieConfiguration;
use Symplify\Statie\Renderable\File\AbstractFile;

abstract class AbstractTemplatingFileDecorator
{
    /**
     * @var StatieConfiguration
     */
    protected $statieConfiguration;

    /**
     * @required
     */
    public function addConfiguration(StatieConfiguration $statieConfiguration): void
    {
        $this->statieConfiguration = $statieConfiguration;
    }

    /**
     * @return mixed[]
     */
    protected function createParameters(AbstractFile $file, string $fileKey): array
    {
        $parameters = $file->getConfiguration();
        $parameters += $this->statieConfiguration->getOptions();
        $parameters[$fileKey] = $file;
        $parameters['layout'] = $file->getLayout();

        return $parameters;
    }
}
