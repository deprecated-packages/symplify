<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable\Routing\Route;

use Symplify\Statie\Configuration\Configuration;
use Symplify\Statie\Contract\Renderable\Routing\Route\RouteInterface;
use Symplify\Statie\Renderable\File\AbstractFile;
use Symplify\Statie\Renderable\File\PostFile;
use Symplify\Statie\Utils\PathNormalizer;

final class PostRoute implements RouteInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function matches(AbstractFile $file) : bool
    {
        return $file instanceof PostFile;
    }

    /**
     * @param PostFile $file
     */
    public function buildOutputPath(AbstractFile $file) : string
    {
        return PathNormalizer::normalize($this->buildRelativeUrl($file) . '/index.html');
    }

    /**
     * @param PostFile $file
     */
    public function buildRelativeUrl(AbstractFile $file) : string
    {
        $permalink = $this->configuration->getPostRoute();
        $permalink = preg_replace('/:year/', $file->getDateInFormat('Y'), $permalink);
        $permalink = preg_replace('/:month/', $file->getDateInFormat('m'), $permalink);
        $permalink = preg_replace('/:day/', $file->getDateInFormat('d'), $permalink);

        return preg_replace('/:title/', $file->getFilenameWithoutDate(), $permalink);
    }
}
