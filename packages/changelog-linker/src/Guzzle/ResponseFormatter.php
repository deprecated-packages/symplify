<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\Guzzle;

use Nette\Utils\Json;
use Psr\Http\Message\ResponseInterface;

final class ResponseFormatter
{
    /**
     * @return mixed[]
     */
    public function formatToJson(ResponseInterface $response): array
    {
        $responseBody = $response->getBody();
        return Json::decode($responseBody->getContents(), Json::FORCE_ARRAY);
    }
}
