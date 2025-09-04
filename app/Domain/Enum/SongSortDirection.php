<?php

namespace App\Domain\Enum;

enum SongSortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';
}
