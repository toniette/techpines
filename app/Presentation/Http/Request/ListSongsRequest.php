<?php

namespace App\Presentation\Http\Request;

use App\Domain\Enum\SongFilterableProperty;
use App\Domain\Enum\SongSortableProperty;
use App\Domain\Enum\SongSortDirection;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Data;

class ListSongsRequest extends Data
{
    public function __construct(
        #[Min(1)]
        public int $page = 1,
        #[Min(1)]
        #[Max(50)]
        public int $perPage = 10,
        public SongSortableProperty $sortBy = SongSortableProperty::CREATED_AT,
        public SongSortDirection $direction = SongSortDirection::DESC,
        public ?SongFilterableProperty $filterBy = null,
        public ?string $filterValue = null,
    )
    {
    }
}
