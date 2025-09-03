<?php

namespace App\Infrastructure\Models;

use App\Domain\Enum\SongStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
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
    protected function ranking(Builder $query): void
    {
        $query->approved()->orderByDesc('views_count')->orderBy('title');
    }
}
