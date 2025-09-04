<?php

namespace App\Presentation\Http\Response;

use Spatie\LaravelData\Data;

class LoginResponse extends Data
{
    public function __construct(
        public string $access_token,
        public string $token_type = 'Bearer',
    ) {}
}
