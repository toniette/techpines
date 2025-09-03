<?php

namespace App\Domain\Entity;

use App\Domain\Enum\SongStatus;
use App\Domain\State\Stateful;
use DateTimeInterface;

/**
 * @method approve()
 * @method reject()
 */
class Song
{
    use Stateful;

    private SongStatus $status;

    public function __construct(
        public string $id,
        public string $title,
        public int $viewsCount = 0,
        public ?string $thumbnailUrl = null,
        public ?DateTimeInterface $createdAt = null,
        public ?DateTimeInterface $updatedAt = null,
        public ?DateTimeInterface $approvedAt = null,
        public ?User $approvedBy = null,
        public ?DateTimeInterface $rejectedAt = null,
        public ?User $rejectedBy = null,
        public ?DateTimeInterface $deletedAt = null,
        public ?User $deletedBy = null,
        SongStatus $status = SongStatus::SUGGESTED,
    )
    {
        $this->status = $status;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'viewsCount' => $this->viewsCount,
            'thumbnailUrl' => $this->thumbnailUrl,
            'status' => $this->status->value,
            'createdAt' => $this->createdAt?->format(DateTimeInterface::ATOM),
            'updatedAt' => $this->updatedAt?->format(DateTimeInterface::ATOM),
            'approvedAt' => $this->approvedAt?->format(DateTimeInterface::ATOM),
            'approvedBy' => $this->approvedBy?->id,
            'rejectedAt' => $this->rejectedAt?->format(DateTimeInterface::ATOM),
            'rejectedBy' => $this->rejectedBy?->id,
            'deletedAt' => $this->deletedAt?->format(DateTimeInterface::ATOM),
            'deletedBy' => $this->deletedBy?->id,
        ];
    }
}
