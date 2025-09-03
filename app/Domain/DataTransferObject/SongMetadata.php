<?php

namespace App\Domain\DataTransferObject;

readonly class SongMetadata
{
    public function __construct(
        public string  $title,
        public ?string $thumbnailUrl = null,
        public int     $viewsCount = 0,
    )
    {
    }
}
