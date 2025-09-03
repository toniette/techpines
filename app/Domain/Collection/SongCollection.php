<?php

namespace App\Domain\Collection;

use App\Domain\Entity\Song;

class SongCollection extends Collection
{
    protected null|string $type = Song::class;
}
