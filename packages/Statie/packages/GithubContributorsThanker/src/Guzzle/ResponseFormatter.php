<?php declare(strict_types=1);

namespace Symplify\Statie\GithubContributorsThanker\Guzzle;

use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;
use Symplify\Statie\GithubContributorsThanker\Exception\Api\ApiException;
use function Safe\sprintf;

final class ResponseFormatter
{
    /**
     * @return mixed[]
     */
    public function formatResponseToJson(ResponseInterface $response, string $originalUrl): array
    {
        if ($response->getStatusCode() >= 400) {
            throw new ApiException(sprintf(
                'Response to "%s" failed with code %d and message: "%s"',
                $originalUrl,
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ));
        }

        return Json::decode((string) $response->getBody(), Json::FORCE_ARRAY);
    }
}
