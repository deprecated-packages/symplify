<?php declare(strict_types=1);

namespace Symplify\Statie\Tests\Helper;

use SplFileInfo;
use Symplify\Statie\Configuration\Parser\NeonParser;
use Symplify\Statie\Renderable\Configuration\ConfigurationDecorator;
use Symplify\Statie\Renderable\File\PostFile;

final class PostFactory
{
    /**
     * @var ConfigurationDecorator
     */
    private $configurationDecorator;

    public function __construct()
    {
        $this->configurationDecorator = new ConfigurationDecorator(new NeonParser);
    }

    public function createPostFromFilePath(string $filePath): PostFile
    {
        $fileInfo = new SplFileInfo($filePath);
        $post = new PostFile($fileInfo, $filePath);

        $this->configurationDecorator->decorateFile($post);

        return $post;
    }
}
