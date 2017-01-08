<?php declare(strict_types=1);

namespace Symplify\Statie\HttpServer\MimeType;

use Mimey\MimeTypes;

final class MimeTypeDetector
{
    /**
     * @var MimeTypes
     */
    private $mimeTypes;

    public function __construct(MimeTypes $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    public function detectForFilename(string $filemame) : string
    {
        if ($extension = pathinfo($filemame, PATHINFO_EXTENSION)) {
            if ($guessedType = $this->mimeTypes->getMimeType($extension)) {
                return $guessedType;
            }
        }

        return 'application/octet-stream';
    }
}
