<?php

namespace App\Presentation\Http\Response;

use Spatie\LaravelData\Data;

class RankSongsResponse extends Data
{
    public function __construct(
        public string $title,
        public int $viewsCount,
        public string $thumbnailUrl,
    ) {}
}
