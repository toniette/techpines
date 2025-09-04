<?php

namespace App\Presentation\Http\Response;

use App\Domain\Enum\SongStatus;
use DateTimeImmutable;
use Spatie\LaravelData\Data;

class ListSongsResponse extends Data
{
    public function __construct(
        public string $id,
        public string $title,
        public int $viewsCount,
        public string $thumbnailUrl,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public ?DateTimeImmutable $approvedAt,
        public ?string $approvedBy,
        public ?DateTimeImmutable $rejectedAt,
        public ?string $rejectedBy,
        public ?DateTimeImmutable $deletedAt,
        public ?string $deletedBy,
        public SongStatus $status,
    ) {}
}
