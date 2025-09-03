<?php

namespace App\Domain\Enum;

enum SongSortableProperty: string
{
    case CREATED_AT = 'createdAt';
    case APPROVED_AT = 'approvedAt';
    case REJECTED_AT = 'rejectedAt';
    case VIEWS_COUNT = 'viewsCount';
}
