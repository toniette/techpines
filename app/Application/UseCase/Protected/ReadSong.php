<?php

namespace App\Application\UseCase\Protected;

use App\Domain\Entity\Song;
use App\Domain\Exception\SongNotFoundException;
use App\Domain\Repository\SongRepository;

class ReadSong
{
    public function __construct(
        protected SongRepository $songRepository
    ) {}

    public function __invoke(string $songId): Song
    {
        $song = $this->songRepository->find($songId);
        if (! $song) {
            throw new SongNotFoundException('Song not found');
        }

        return $song;
    }
}
