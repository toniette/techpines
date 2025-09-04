<?php

namespace App\Infrastructure;

use App\Domain\DataTransferObject\SongMetadata;
use App\Domain\Exception\SongDataRetrievingException;
use App\Domain\Repository\SongMetadataRetriever;
use App\Domain\ValueObject\YoutubeLink;
use App\Infrastructure\External\Http\YoutubeClient;
use Illuminate\Log\Logger;
use Illuminate\Support\Arr;

class ApiSongMetadataRetriever implements SongMetadataRetriever
{
    public function __construct(
        protected YoutubeClient $client,
        protected Logger $logger,
    ) {}

    public function retrieve(YoutubeLink $link): SongMetadata
    {
        $data = $this->client->fetchVideoData($link);
        $video = Arr::first(data_get($data, 'items', []), fn ($item) => data_get($item, 'id') === $link->id());

        if (! $video) {
            $this->logger->error(
                'Video not found in YouTube API response',
                ['link' => (string) $link, 'response' => $data]
            );
            throw new SongDataRetrievingException('Video not found in YouTube API response');
        }

        return new SongMetadata(
            title: (string) data_get($video, 'snippet.title', 'Unknown Title'),
            thumbnailUrl: (string) data_get($video, 'snippet.thumbnails.high.url', ''),
            viewsCount: (int) data_get($video, 'statistics.viewCount', 0),
            id: $link->id(),
        );
    }
}
