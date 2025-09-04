<?php

namespace App\Presentation\Http\Response;

use App\Domain\Entity\User;
use App\Domain\Enum\SongStatus;
use DateTimeInterface;
use Spatie\LaravelData\Data;

class RankSongsResponse extends Data
{
    public function __construct(
        public string $title,
        public int    $viewsCount,
        public string $thumbnailUrl,
    )
    {
    }
}
