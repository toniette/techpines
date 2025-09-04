<?php

namespace App\Presentation\Http\Request;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class RankSongsRequest extends Data
{
    public function __construct(
        #[Min(1)]
        public int $page = 1,
        #[Min(1)]
        #[Max(20)]
        public int $perPage = 10,
    ) {}
}
