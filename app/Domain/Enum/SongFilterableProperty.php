<?php

namespace App\Domain\Enum;

enum SongFilterableProperty: string
{
    case STATUS = 'status';
    case APPROVED_BY = 'approvedBy';
    case REJECTED_BY = 'rejectedBy';
}
