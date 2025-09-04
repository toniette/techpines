<?php

namespace App\Domain\DataTransferObject;

use App\Domain\Exception\InvalidSongDataException;

readonly class SongMetadata
{
    public function __construct(
        public string $title,
        public string $thumbnailUrl,
        public int $viewsCount = 0,
        public ?string $id = null,
    ) {
        if (empty($this->title)) {
            throw new InvalidSongDataException('Title cannot be empty');
        }

        if (strlen($this->title) > 255) {
            throw new InvalidSongDataException('Title cannot be longer than 255 characters');
        }

        if ($this->viewsCount < 0) {
            throw new InvalidSongDataException('Views count cannot be negative');
        }

        if ($this->id !== null && empty($this->id)) {
            throw new InvalidSongDataException('ID cannot be an empty string');
        }

        if (empty($this->thumbnailUrl)) {
            throw new InvalidSongDataException('Thumbnail URL cannot be an empty string');
        }

        if (! filter_var($this->thumbnailUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidSongDataException('Thumbnail URL is not valid');
        }

        if (strlen($this->thumbnailUrl) > 255) {
            throw new InvalidSongDataException('Thumbnail URL cannot be longer than 255 characters');
        }
    }
}
