<?php

namespace App\Application\UseCase\Protected;

use App\Domain\Entity\User;
use App\Domain\Exception\SongNotFoundException;
use App\Domain\Repository\SongRepository;

class RejectSong
{
    public function __construct(
        protected SongRepository $songRepository,
        protected User $user,
    ) {}

    public function __invoke(string $songId): void
    {
        $song = $this->songRepository->find($songId);
        if (! $song) {
            throw new SongNotFoundException('Song not found');
        }

        $this->user->rejectSong($song);

        $songRepository = $this->songRepository;
        $songRepository->save($song);
    }
}
