<?php

namespace App\Presentation\Http\Controllers;

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

    public function login()
    {
        $credentials = $this->request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            $this->response->setStatusCode(401);
            return $this->response;
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->response->setStatusCode(200);
        $this->response->setContent(['access_token' => $token, 'token_type' => 'Bearer']);
        return $this->response;
    }
}
