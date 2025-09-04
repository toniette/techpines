<?php

namespace App\Domain\ValueObject;

use App\Domain\Exception\InvalidYoutubeLinkException;
use Stringable;

readonly class YoutubeLink implements Stringable
{
    private ?string $id;

    public function __construct(
        private string $url
    ) {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidYoutubeLinkException('Invalid URL format');
        }

        $pattern = '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([A-Za-z0-9_-]{11})/';
        if (! preg_match($pattern, $url, $matches)) {
            throw new InvalidYoutubeLinkException('URL is not a valid YouTube link');
        }

        $this->id = $matches[1];
    }

    public function url(): string
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }
}
