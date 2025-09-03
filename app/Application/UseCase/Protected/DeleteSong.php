<?php

namespace App\Application\UseCase\Protected;

use App\Domain\Entity\User;
use App\Domain\Repository\SongRepository;
use Exception;

class DeleteSong
{
    public function __construct(
        protected SongRepository $songRepository,
        protected User $user,
    )
    {
    }

    public function __invoke(string $songId): void
    {
        $song = $this->songRepository->find($songId);
        if (!$song) {
            throw new Exception("Song not found");
        }

        $this->user->deleteSong($song);

        $this->songRepository->save($song);
    }
}
