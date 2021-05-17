<?php

// inspired at https://github.com/phpstan/phpstan-src/commit/87897c2a4980d68efa1c46049ac2eefe767ec946#diff-e897e523125a694bd8ea69bf83374c206803c98720c46d7401b7a7cf53915a26
// and https://github.com/rectorphp/rector-src/blob/main/build/build-preload.php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symfony\Component\Finder\Finder;

require __DIR__ . '/../vendor/autoload.php';

$buildDirectory = $argv[1];

buildPreloadScript($buildDirectory);

function buildPreloadScript(string $buildDirectory): void
{
    $vendorDir = $buildDirectory . '/vendor';

    $preloadFileContent = <<<'CODE_SAMPLE'
<?php

declare(strict_types = 1);


CODE_SAMPLE;

    $finder = (new Finder())
        ->files()
        ->name('*.php')
        ->notPath('#\/tests\/#')
        ->notPath('#\/Tests\/#')
        ->notPath('#\/config\/#')
        ->notPath('#\/set\/#')
        ->in($vendorDir . '/psr/container')
        ->in($vendorDir . '/symplify')
        ->in($vendorDir . '/friendsofphp')
        ->in($vendorDir . '/squizlabs')
        ->in($vendorDir . '/symfony');

    /** @var \Symfony\Component\Finder\SplFileInfo[] $fileInfos */
    $fileInfos = iterator_to_array($finder->getIterator());

    foreach ($fileInfos as $fileInfo) {
        $realPath = $fileInfo->getRealPath();
        $filePath = '/vendor/' . Strings::after($realPath, 'vendor/');
        $preloadFileContent .= "require_once __DIR__ . '" . $filePath . "';" . PHP_EOL;
    }

    file_put_contents($buildDirectory . '/preload.php', $preloadFileContent);
}
