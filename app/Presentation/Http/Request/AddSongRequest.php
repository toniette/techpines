<?php

namespace App\Presentation\Http\Request;

use Spatie\LaravelData\Attributes\Validation\ActiveUrl;
use Spatie\LaravelData\Data;

class AddSongRequest extends Data
{
    public function __construct(
        #[ActiveUrl]
        public string $link
    ) {}
}
