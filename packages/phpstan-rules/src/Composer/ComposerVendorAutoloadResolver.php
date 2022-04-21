<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Composer;

final class ComposerVendorAutoloadResolver
{
    /**
     * @var string
     */
    private const COMPOSER_JSON_FILE = './vendor/composer/autoload_psr4.php';

    /**
     * @return array<string, string[]|string>
     */
    public function getPsr4Autoload(): array
    {
        if (! file_exists(self::COMPOSER_JSON_FILE)) {
            return [];
        }

        return require self::COMPOSER_JSON_FILE;
    }
}
