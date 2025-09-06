<?php

namespace App\Infrastructure\Repository;

use App\Domain\Collection\SongCollection;
use App\Domain\Entity\Song;
use App\Domain\Entity\User;
use App\Domain\Enum\SongFilterableProperty;
use App\Domain\Enum\SongSortableProperty;
use App\Domain\Enum\SongSortDirection;
use App\Domain\Repository\SongRepository;
use App\Infrastructure\Enum\CacheKey;
use App\Infrastructure\Models\Song as SongModel;
use DateInterval;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentSongRepository implements SongRepository
{
    public function __construct(
        protected SongModel $model,
        protected CacheRepository $cache
    ) {}

    public function save(Song $song): Song
    {
        $this->model->updateOrCreate(
            ['id' => $song->id],
            [
                'title' => $song->title,
                'views_count' => $song->viewsCount,
                'status' => $song->status,
                'thumbnail_url' => $song->thumbnailUrl,
                'approved_at' => $song->approvedAt,
                'approved_by' => $song->approvedBy?->id,
                'rejected_at' => $song->rejectedAt,
                'rejected_by' => $song->rejectedBy?->id,
                'deleted_at' => $song->deletedAt,
                'deleted_by' => $song->deletedBy?->id,
            ]
        );

        $this->cache->forget(CacheKey::SONG_BY_ID->with($song->id));

        return $this->find($song->id) ?? $song;
    }

    public function find(string $id): ?Song
    {
        /** @var SongModel|null $song */
        $song = $this->cache->remember(
            key: CacheKey::SONG_BY_ID->with($id),
            ttl: new DateInterval('PT10M'),
            callback: fn () => $this->model->find($id)
        );

        if (! $song) {
            return null;
        }

        return $this->toEntity($song);
    }

    protected function toEntity(SongModel $model): Song
    {
        return new Song(
            id: $model->id,
            title: $model->title,
            viewsCount: $model->views_count,
            thumbnailUrl: $model->thumbnail_url,
            createdAt: $model->created_at,
            updatedAt: $model->updated_at,
            approvedAt: $model->approved_at,
            approvedBy: $model->approved_by ? new User(id: $model->approved_by) : null,
            rejectedAt: $model->rejected_at,
            rejectedBy: $model->rejected_by ? new User(id: $model->rejected_by) : null,
            deletedAt: $model->deleted_at,
            deletedBy: $model->deleted_by ? new User(id: $model->deleted_by) : null,
            status: $model->status,
        );
    }

    public function delete(Song $song): void
    {
        $this->model->where('id', $song->id)->delete();
    }

    public function rank(int $page = 1, int $perPage = 5): SongCollection
    {
        /** @var LengthAwarePaginator $songs */
        $songs = $this->cache->remember(
            key: CacheKey::RANK_SONGS_PAGE->with((string) $page, (string) $perPage),
            ttl: new DateInterval('PT10M'),
            callback: fn () => $this->model
                ->ranking()
                ->orderBy('views_count', 'desc')
                ->paginate(perPage: $perPage, page: $page)
        );

        return new SongCollection(...array_map(
            fn ($song) => $this->toEntity($song),
            $songs->items()
        ));
    }

    public function paginate(
        int $page = 1,
        int $perPage = 5,
        SongSortableProperty $sortBy = SongSortableProperty::CREATED_AT,
        SongSortDirection $direction = SongSortDirection::DESC,
        ?SongFilterableProperty $filterBy = null,
        ?string $filterValue = null,
    ): SongCollection {
        $cacheKey = CacheKey::LIST_SONGS_PAGE->with(
            (string) $page,
            (string) $perPage,
            $filterBy?->value ?? 'null',
            $filterValue ?? 'null',
            $sortBy->value,
            $direction->value
        );

        /** @var LengthAwarePaginator $songs */
        $songs = $this->cache->remember(
            key: $cacheKey,
            ttl: new DateInterval('PT1M'),
            callback: fn () => $this->model
                ->filteredBy($filterBy, $filterValue)
                ->sortedBy($sortBy, $direction)
                ->paginate(perPage: $perPage, page: $page)
        );

        return new SongCollection(...array_map(
            fn ($song) => $this->toEntity($song),
            $songs->items()
        ));
    }
}
