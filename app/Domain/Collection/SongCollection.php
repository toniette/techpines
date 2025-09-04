<?php

namespace App\Domain\Collection;

use App\Domain\Entity\Song;

class SongCollection extends Collection
{
    protected ?string $type = Song::class;

    public function toArray(): array
    {
        return array_map(fn (Song $song) => $song->toArray(), iterator_to_array($this));
    }
}
