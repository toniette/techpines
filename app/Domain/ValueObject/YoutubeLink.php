<?php

namespace App\Domain\ValueObject;

use InvalidArgumentException;
use Stringable;

readonly class YoutubeLink implements Stringable
{
    public function __construct(
        private string $url
    )
    {
        // Validate the URL format
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL format");
        }

        $pattern = '/^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)[^&]+/';
        if (!preg_match($pattern, $url)) {
            throw new InvalidArgumentException("URL is not a valid YouTube link");
        }
    }

    public function id(): string
    {
        // Extract the video ID from the YouTube URL
        parse_str(parse_url($this->url, PHP_URL_QUERY), $queryParams);
        return $queryParams['v'] ?? '';
    }

    public function __toString(): string
    {
        return $this->id();
    }
}
