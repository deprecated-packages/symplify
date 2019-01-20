<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Json;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\Renderable\File\VirtualFile;

final class ApiGenerator
{
    /**
     * @var string[]
     */
    private $apiParameters = [];

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @param string[] $apiParameters
     */
    public function __construct(array $apiParameters, ParameterProvider $parameterProvider)
    {
        $this->apiParameters = $apiParameters;
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * @return VirtualFile[]
     */
    public function generate(): array
    {
        $virtualFiles = [];
        foreach ($this->apiParameters as $apiParameter) {
            $outputPath = $this->createOutputPath($apiParameter);
            $content = $this->createContent($apiParameter);

            $virtualFiles[] = new VirtualFile($outputPath, $content);
        }

        return $virtualFiles;
    }

    private function createOutputPath(string $parameter): string
    {
        return 'api/' . $parameter . '.json';
    }

    private function createContent(string $parameterName): string
    {
        $parameter = $this->parameterProvider->provideParameter($parameterName);

        $content = [
            $parameterName => is_array($parameter) ? $parameter : '{}',
        ];

        return Json::encode($content, Json::PRETTY);
    }
}
