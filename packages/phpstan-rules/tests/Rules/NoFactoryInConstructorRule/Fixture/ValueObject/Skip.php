<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;

final class Skip
{
    /**
     * @var string
     */
    private $basePath;

    public function __construct(SmartFileInfo $fileInfo)
    {
        $this->basePath = $fileInfo->getPath();
    }
}
