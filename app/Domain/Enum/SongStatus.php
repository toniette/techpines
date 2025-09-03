<?php

namespace App\Domain\Enum;

use App\Domain\State\State;
use App\Domain\State\Transition;
use App\Domain\State\TransitionCollection;

enum SongStatus: string implements State
{
    #[TransitionCollection(
        new Transition('approve', self::APPROVED),
        new Transition('reject', self::REJECTED),
    )]
    case SUGGESTED = 'suggested';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
}
