<?php

namespace App\Presentation\Http\Controllers;

use App\Presentation\Http\Request\LoginRequest;
use App\Presentation\Http\Response\LoginResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticationController
{
    public function __construct(
        protected Request $request,
        protected Response $response,
    )
    {
    }

    public function login(LoginRequest $input): Response
    {
        if (!Auth::attempt(['email' => $input->email, 'password' => $input->password])) {
            $this->response->setStatusCode(401);
            return $this->response;
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->response->setStatusCode(200);
        $this->response->setContent(new LoginResponse($token)->toArray());
        return $this->response;
    }
}
