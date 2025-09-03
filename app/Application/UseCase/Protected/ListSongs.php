<?php

namespace App\Application\UseCase\Protected;

use App\Domain\Collection\SongCollection;
use App\Domain\Enum\SongFilterableProperty;
use App\Domain\Enum\SongSortableProperty;
use App\Domain\Enum\SongSortDirection;
use App\Domain\Repository\SongRepository;

class ListSongs
{
    public function __construct(
        protected SongRepository $songRepository,
    )
    {
    }

    public function __invoke(
        int                     $page,
        int                     $perPage,
        SongSortableProperty    $sortBy = SongSortableProperty::CREATED_AT,
        SongSortDirection       $direction = SongSortDirection::DESC,
        ?SongFilterableProperty $filterBy = null,
        ?string                 $filterValue = null,
    ): SongCollection
    {
        return $this->songRepository->paginate(
            $page,
            $perPage,
            $sortBy,
            $direction,
            $filterBy,
            $filterValue
        );
    }
}
