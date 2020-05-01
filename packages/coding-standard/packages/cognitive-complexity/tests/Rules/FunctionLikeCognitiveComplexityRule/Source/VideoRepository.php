<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\Tests\Rules\FunctionLikeCognitiveComplexityRule\Source;

final class VideoRepository
{
    /**
     * Total complexity: 9
     */
    public function findBySlug(string $slug): object
    {
        foreach ($this->livestreamVideos as $livestreamVideo) { // operation: +1
            if ($livestreamVideo->getSlug() !== $slug) { // operation: +1, nesting: +1
                continue;
            }

            return $livestreamVideo; // 1
        }

        $recodedEvents = array_merge($this->recordedMeetups, $this->recordedConferences);

        foreach ($recodedEvents as $recodedEvent) { // operation: +1
            foreach ($recodedEvent->getVideos() as $video) { // operation: +1, nesting: +1
                if ($video->getSlug() !== $slug) { // operation: +1, nesting: +2
                    continue;
                }

                return $video;
            }
        }

        throw new VideoNotFoundException($slug);
    }
}
