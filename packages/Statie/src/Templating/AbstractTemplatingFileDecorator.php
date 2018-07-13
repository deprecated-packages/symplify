<?php declare(strict_types=1);

namespace Symplify\Statie\Templating;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Renderable\File\AbstractFile;

abstract class AbstractTemplatingFileDecorator
{
    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @required
     */
    public function addConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }

    /**
     * @return mixed[]
     */
    protected function createParameters(AbstractFile $file, string $fileKey): array
    {
        $parameters = $file->getConfiguration();
        $parameters += $this->configuration->getOptions();
        $parameters[$fileKey] = $file;
        $parameters['layout'] = $file->getLayout();

        return $parameters;
    }
}
