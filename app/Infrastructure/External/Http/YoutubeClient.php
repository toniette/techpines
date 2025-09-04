<?php

namespace App\Infrastructure\External\Http;

use App\Domain\Exception\SongDataRetrievingException;
use App\Domain\ValueObject\YoutubeLink;
use Illuminate\Config\Repository;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Log\Logger;
use Throwable;

class YoutubeClient extends PendingRequest
{
    private readonly string $apiKey;

    public function __construct(
        protected Repository $config,
        protected Logger $logger,
    ) {
        parent::__construct();

        $this->baseUrl($this->config->get('youtube.api.base_url'));
        $this->apiKey = $this->config->get('youtube.api.key');
    }

    public function fetchVideoData(YoutubeLink $link): array
    {
        try {
            return $this->get('videos', [
                'part' => 'snippet,statistics',
                'id' => $link->id(),
                'key' => $this->apiKey,
            ])->throw()->json();
        } catch (Throwable $throwable) {
            $message = 'Error fetching video data from YouTube API';
            $this->logger->error($message, [
                'error' => $throwable->getMessage(),
                'link' => (string) $link,
            ]);
            throw new SongDataRetrievingException($message, previous: $throwable);
        }
    }
}
