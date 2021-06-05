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
        // order matters, as lower files depend on higher ones
        ->in($vendorDir . '/psr/container');

    /** @var \Symfony\Component\Finder\SplFileInfo[] $fileInfos */
    $fileInfos = iterator_to_array($finder->getIterator());

    // must be in specific order, as included class needs the classes it contains
    $fileInfos[] = new SplFileInfo(
        $vendorDir . '/symplify/rule-doc-generator-contracts/src/ValueObject/RuleDefinition.php'
    );
    $fileInfos[] = new SplFileInfo(
        $vendorDir . '/symplify/rule-doc-generator-contracts/src/Contract/DocumentedRuleInterface.php'
    );

    $fileInfos[] = new SplFileInfo(
        $vendorDir . '/symfony/dependency-injection/Loader/Configurator/AbstractConfigurator.php',
    );
    $fileInfos[] = new SplFileInfo(
        $vendorDir . '/symfony/dependency-injection/Loader/Configurator/ContainerConfigurator.php'
    );

    foreach ($fileInfos as $fileInfo) {
        $realPath = $fileInfo->getRealPath();
        $filePath = '/vendor/' . Strings::after($realPath, 'vendor/');
        $preloadFileContent .= "require_once __DIR__ . '" . $filePath . "';" . PHP_EOL;
    }

    file_put_contents($buildDirectory . '/preload.php', $preloadFileContent);
}
