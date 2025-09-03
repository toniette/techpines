<?php

namespace App\Domain\Repository;

use App\Domain\Collection\SongCollection;
use App\Domain\Entity\Song;

interface SongRepository
{
    public function save(Song $song): Song;
    public function delete(Song $song): void;
    public function find(string $id): ?Song;
    public function paginate(int $page = 1, int $perPage = 5): SongCollection;
    public function rank(int $page = 1, int $perPage = 5): SongCollection;
}
