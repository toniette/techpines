<?php

namespace App\Application\UseCase\Protected;

use App\Domain\DataTransferObject\SongMetadata;
use App\Domain\Entity\Song;
use App\Domain\Entity\User;
use App\Domain\Exception\SongNotFoundException;
use App\Domain\Repository\SongRepository;

class UpdateSong
{
    public function __construct(
        protected SongRepository $songRepository,
        protected User $user,
    )
    {
    }

    public function __invoke(string $songId, SongMetadata $metadata): Song
    {
        $song = $this->songRepository->find($songId);

        if (!$song) {
            throw new SongNotFoundException("Song not found");
        }

        $this->user->updateSong($song, $metadata);

        return $this->songRepository->save($song);
    }
}
