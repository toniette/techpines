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

    public function approveSong(Song $song): Song
    {
        $song->approve();
        $song->approvedAt = new DateTimeImmutable();
        $song->approvedBy = $this;

        return $song;
    }

    public function rejectSong(Song $song): Song
    {
        $song->reject();
        $song->rejectedAt = new DateTimeImmutable();
        $song->rejectedBy = $this;

        return $song;
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

    public function deleteSong(Song $song): Song
    {
        $song->deletedAt = new DateTimeImmutable();
        $song->deletedBy = $this;

        return $song;
    }
}
