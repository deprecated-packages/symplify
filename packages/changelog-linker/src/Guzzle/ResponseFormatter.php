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
        $stream = $response->getBody();
        return Json::decode($stream->getContents(), Json::FORCE_ARRAY);
    }
}
