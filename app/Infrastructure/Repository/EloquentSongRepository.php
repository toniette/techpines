<?php

namespace App\Infrastructure\Repository;

use App\Domain\Collection\SongCollection;
use App\Domain\Entity\Song;
use App\Domain\Enum\SongStatus;
use App\Domain\Repository\SongRepository;
use App\Infrastructure\Enum\CacheKey;
use App\Infrastructure\Models\Song as SongModel;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use DateInterval;

class EloquentSongRepository implements SongRepository
{
    public function __construct(
        protected SongModel $model,
        protected CacheRepository $cache
    )
    {
    }

    public function save(Song $song): Song
    {
        $this->model->firstOrCreate(
            ['id' => $song->id],
            [
                'title' => $song->title,
                'views_count' => $song->viewsCount,
                'status' => $song->status,
            ]
        );

        return $song;
    }

    public function delete(Song $song): void
    {
        $this->model->where('id', $song->id)->delete();
    }

    public function find(string $id): ?Song
    {
        /** @var SongModel|null $song */
        $song = $this->cache->remember(
            key: CacheKey::SONG_BY_ID->with($id),
            ttl: new DateInterval('PT10M'),
            callback: fn () => $this->model->find($id)
        );

        if (!$song) {
            return null;
        }

        return new Song(
            id: $song->id,
            title: $song->title,
            viewsCount: $song->views_count,
            status: $song->status,
        );
    }

    public function paginate(int $page = 1, int $perPage = 5): SongCollection
    {
        /** @var LengthAwarePaginator $songs */
        $songs = $this->cache->remember(
            key: CacheKey::LIST_SONGS_PAGE->with((string)$page, (string)$perPage),
            ttl: new DateInterval('PT10M'),
            callback: fn () => $this->model->paginate(perPage: $perPage, page: $page)
        );

        return new SongCollection(...array_map(
            fn ($song) => new Song(
                id: $song->id,
                title: $song->title,
                viewsCount: $song->views_count,
                status: $song->status,
            ),
            $songs->items()
        ));
    }

    public function rank(int $page = 1, int $perPage = 5): SongCollection
    {
        /** @var LengthAwarePaginator $songs */
        $songs = $this->cache->remember(
            key: CacheKey::RANK_SONGS_PAGE->with((string)$page, (string)$perPage),
            ttl: new DateInterval('PT10M'),
            callback: fn () => $this->model
                ->ranking()
                ->orderBy('views_count', 'desc')
                ->paginate(perPage: $perPage, page: $page)
        );

        return new SongCollection(...array_map(
            fn ($song) => new Song(
                id: $song->id,
                title: $song->title,
                viewsCount: $song->views_count,
                status: $song->status,
            ),
            $songs->items()
        ));
    }
}
