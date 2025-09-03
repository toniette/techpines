<?php

namespace App\Infrastructure\Models;

use App\Domain\Enum\SongFilterableProperty;
use App\Domain\Enum\SongSortableProperty;
use App\Domain\Enum\SongSortDirection;
use App\Domain\Enum\SongStatus;
use Database\Factories\SongFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UseFactory(SongFactory::class)]
class Song extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'deleted_at' => 'datetime',
        'views_count' => 'integer',
        'status' => SongStatus::class,
    ];

    protected $fillable = [
        'id',
        'title',
        'views_count',
        'thumbnail_url',
        'status',
        'created_at',
        'updated_at',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'deleted_at',
        'deleted_by'
    ];

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->whereStatus(SongStatus::APPROVED);
    }

    #[Scope]
    protected function rejected(Builder $query): void
    {
        $query->whereStatus(SongStatus::REJECTED);
    }

    #[Scope]
    protected function suggested(Builder $query): void
    {
        $query->whereStatus(SongStatus::SUGGESTED);
    }

    #[Scope]
    protected function sortedBy(Builder $query, SongSortableProperty $property, SongSortDirection $order): void
    {
        $direction = match ($order) {
            SongSortDirection::ASC => 'asc',
            SongSortDirection::DESC => 'desc',
        };


        $column = match ($property) {
            SongSortableProperty::CREATED_AT => 'created_at',
            SongSortableProperty::APPROVED_AT => 'approved_at',
            SongSortableProperty::REJECTED_AT => 'rejected_at',
            SongSortableProperty::VIEWS_COUNT => 'views_count',
        };

        $query->orderBy($column, $direction);
    }

    #[Scope]
    protected function filteredBy(
        Builder $query,
        ?SongFilterableProperty $property = null,
        ?string $value = null
    ): void
    {
        if ($property === null || $value === null) {
            return;
        }

        match ($property) {
            SongFilterableProperty::STATUS => $query->whereStatus($value),
            SongFilterableProperty::APPROVED_BY => $query->approvedBy($value),
            SongFilterableProperty::REJECTED_BY => $query->rejectedBy($value),
        };
    }

    #[Scope]
    protected function ranking(Builder $query): void
    {
        $query->approved()->orderByDesc('views_count')->orderBy('title');
    }

    #[Scope]
    protected function approvedBy($query, string|int $userId): void
    {
        $query->where('approved_by', $userId);
    }

    #[Scope]
    protected function rejectedBy($query, string|int $userId): void
    {
        $query->where('rejected_by', $userId);
    }
}
