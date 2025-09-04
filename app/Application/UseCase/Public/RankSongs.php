<?php

namespace App\Application\UseCase\Public;

use App\Domain\Collection\SongCollection;
use App\Domain\Repository\SongRepository;

class RankSongs
{
    public function __construct(
        protected SongRepository $songRepository,
    ) {}

    public function __invoke(int $page, int $perPage): SongCollection
    {
        return $this->songRepository->rank($page, $perPage);
    }
}
