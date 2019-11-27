<?php declare(strict_types=1);

namespace Symplify\Statie\Renderable;

use Nette\Utils\Json;
use Symplify\PackageBuilder\Configuration\EolConfiguration;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\Statie\Contract\Api\ApiItemDecoratorInterface;
use Symplify\Statie\Renderable\File\VirtualFile;

final class ApiGenerator
{
    /**
     * @var string[]
     */
    private $apiParameters = [];

    /**
     * @var ApiItemDecoratorInterface[]
     */
    private $apiItemDecorators = [];

    /**
     * @var ParameterProvider
     */
    private $parameterProvider;

    /**
     * @param string[] $apiParameters
     * @param ApiItemDecoratorInterface[] $apiItemDecorators
     */
    public function __construct(
        array $apiParameters,
        ParameterProvider $parameterProvider,
        array $apiItemDecorators = []
    ) {
        $this->apiParameters = $apiParameters;
        $this->parameterProvider = $parameterProvider;
        $this->apiItemDecorators = $apiItemDecorators;
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

            $virtualFiles[] = new VirtualFile($outputPath, $content . EolConfiguration::getEolChar());
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

        /** @var mixed[] $parameter */
        $parameter = is_array($parameter) ? $parameter : [];

        $data = [$parameterName => $parameter];
        $data = $this->decorateParameter($parameterName, $data);

        return Json::encode($data, Json::PRETTY);
    }

    /**
     * @param mixed[] $parameter
     * @return mixed[]
     */
    private function decorateParameter(string $parameterName, array $parameter): array
    {
        foreach ($this->apiItemDecorators as $apiItemDecorator) {
            if ($parameterName !== $apiItemDecorator->getName()) {
                continue;
            }

            $parameter = $apiItemDecorator->decorate($parameter);
        }

        return $parameter;
    }
}
