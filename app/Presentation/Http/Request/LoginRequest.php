<?php

namespace App\Presentation\Http\Request;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Data;

class LoginRequest extends Data
{
    public function __construct(
        #[Email]
        public string $email,
        public string $password,
    )
    {
    }
}
