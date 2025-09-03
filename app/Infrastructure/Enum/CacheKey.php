<?php

namespace App\Infrastructure\Enum;

enum CacheKey: string
{
    case RANK_SONGS_PAGE = "songs:rank:page:%s:perPage:%s";
    case LIST_SONGS_PAGE = "songs:list:page:%s:perPage:%s:filterBy:%s:filterValue:%s:sortBy:%s:direction:%s";
    case SONG_BY_ID = "songs:id:%s";

    public function with(string ...$params): string
    {
        return sprintf($this->value, ...$params);
    }

}
