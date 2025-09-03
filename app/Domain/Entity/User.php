<?php

namespace App\Domain\Entity;

use App\Domain\DataTransferObject\SongMetadata;
use App\Domain\ValueObject\YoutubeLink;
use DateTimeImmutable;

class User
{
    public function __construct(
        public readonly string $id,
    )
    {
    }

    public function approveSong(Song $song): void
    {
        $song->approve();
        $song->approvedAt = new DateTimeImmutable();
        $song->approvedBy = $this;
    }

    public function rejectSong(Song $song): void
    {
        $song->reject();
        $song->rejectedAt = new DateTimeImmutable();
        $song->rejectedBy = $this;
    }

    public function addSong(SongMetadata $data, bool $approve = false): Song
    {
        $song = new Song(
            id: $data->id,
            title: $data->title,
            viewsCount: $data->viewsCount,
            thumbnailUrl: $data->thumbnailUrl,
        );

        if ($approve) {
            $this->approveSong($song);
        }

        return $song;
    }

    public function updateSong(Song $song, SongMetadata $data): Song
    {
        $song->title = $data->title;
        $song->viewsCount = $data->viewsCount;
        $song->thumbnailUrl = $data->thumbnailUrl;

        return $song;
    }

    public function deleteSong(Song $song)
    {
        $song->deletedAt = new DateTimeImmutable();
        $song->deletedBy = $this;
    }
}
