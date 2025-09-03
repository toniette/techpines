<?php

namespace App\Domain\Entity;

use App\Domain\Enum\SongStatus;
use App\Domain\State\Stateful;

class Song
{
    use Stateful;

    private SongStatus $status;

    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly int $viewsCount = 0,
        SongStatus $status = SongStatus::SUGGESTED,
    )
    {
        $this->status = $status;
    }
}
