<?php

namespace App\Domain\Repository;

use App\Domain\Collection\SongCollection;
use App\Domain\Entity\Song;
use App\Domain\Enum\SongFilterableProperty;
use App\Domain\Enum\SongSortableProperty;
use App\Domain\Enum\SongSortDirection;

interface SongRepository
{
    public function save(Song $song): Song;

    public function delete(Song $song): void;

    public function find(string $id): ?Song;

    public function paginate(
        int                     $page = 1,
        int                     $perPage = 5,
        SongSortableProperty    $sortBy = SongSortableProperty::CREATED_AT,
        SongSortDirection       $direction = SongSortDirection::DESC,
        ?SongFilterableProperty $filterBy = null,
        ?string                 $filterValue = null,
    ): SongCollection;

    public function rank(int $page = 1, int $perPage = 5): SongCollection;
}
