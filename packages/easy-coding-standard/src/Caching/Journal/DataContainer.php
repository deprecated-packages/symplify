<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Caching\Journal;

use Nette\Utils\Json;

class DataContainer
{
    /** @var mixed[] */
    public $tagsByKey = [];

    /** @var array[] */
    public $keysByTag = [];

    /** @var int[] */
    public $prioritiesByKey = [];

    /** @var array[] */
    public $keysByPriority = [];

    public static function fromJson(string $jsonString): self
    {
        $data = Json::decode($jsonString, Json::FORCE_ARRAY);

        $instance = new self();
        $instance->tagsByKey = $data['tags']['by-key'];
        $instance->keysByTag = $data['tags']['by-tag'];
        $instance->prioritiesByKey = $data['priorities']['by-key'];
        $instance->keysByPriority = $data['priorities']['by-priority'];

        return $instance;
    }

    public function toJson(): string
    {
        return Json::encode([
            'tags' => [
                'by-key' => $this->tagsByKey,
                'by-tag' => $this->keysByTag,
            ],
            'priorities' => [
                'by-key' => $this->prioritiesByKey,
                'by-priority' => $this->keysByPriority,
            ],
        ]);
    }
}
