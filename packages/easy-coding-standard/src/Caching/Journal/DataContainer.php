<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching\Journal;

use Nette\Utils\Json;

final class DataContainer
{
    /**
     * @var string
     */
    private const TAGS = 'tags';

    /**
     * @var string
     */
    private const BY_KEY = 'by-key';

    /**
     * @var string
     */
    private const PRIORITIES = 'priorities';

    /**
     * @var mixed[]
     */
    public $tagsByKey = [];

    /**
     * @var array[]
     */
    public $keysByTag = [];

    /**
     * @var int[]
     */
    public $prioritiesByKey = [];

    /**
     * @var array[]
     */
    public $keysByPriority = [];

    public static function fromJson(string $jsonString): self
    {
        $data = Json::decode($jsonString, Json::FORCE_ARRAY);

        $self = new self();
        $self->tagsByKey = $data[self::TAGS][self::BY_KEY];
        $self->keysByTag = $data[self::TAGS]['by-tag'];
        $self->prioritiesByKey = $data[self::PRIORITIES][self::BY_KEY];
        $self->keysByPriority = $data[self::PRIORITIES]['by-priority'];

        return $self;
    }

    public function toJson(): string
    {
        return Json::encode([
            self::TAGS => [
                self::BY_KEY => $this->tagsByKey,
                'by-tag' => $this->keysByTag,
            ],
            self::PRIORITIES => [
                self::BY_KEY => $this->prioritiesByKey,
                'by-priority' => $this->keysByPriority,
            ],
        ]);
    }
}
