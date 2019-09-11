<?php declare(strict_types=1);

namespace Symplify\Statie\JoindIn\Api;

use Symplify\PackageBuilder\Http\BetterGuzzleClient;
use Symplify\Statie\JoindIn\Exception\JoindInException;

final class JoindInApi
{
    /**
     * @var string
     */
    private const API_USER = 'http://api.joind.in/v2.1/users?username=%s';

    /**
     * @var BetterGuzzleClient
     */
    private $betterGuzzleClient;

    public function __construct(BetterGuzzleClient $betterGuzzleClient)
    {
        $this->betterGuzzleClient = $betterGuzzleClient;
    }

    /**
     * @return mixed[]
     */
    public function getTalks(string $username): array
    {
        $url = sprintf(self::API_USER, $username);
        $userJson = $this->betterGuzzleClient->requestToJson($url);
        $this->ensureUsernameIsCorrect($username, $userJson);

        $talksUrl = $userJson['users'][0]['talks_uri'];
        $talksUrl .= '?verbose=yes'; // this includes slides links etc.
        $talksJson = $this->betterGuzzleClient->requestToJson($talksUrl);

        return $talksJson['talks'] ?? [];
    }

    /**
     * @param mixed[] $json
     */
    private function ensureUsernameIsCorrect(string $username, array $json): void
    {
        if ($json['meta']['count'] >= 1) {
            return;
        }

        throw new JoindInException(sprintf('No user was found for "%s" name.', $username));
    }
}
