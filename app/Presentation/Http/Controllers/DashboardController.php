<?php

namespace App\Presentation\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DashboardController
{
    public function __construct(
        protected Request $request,
        protected Response $response
    )
    {
    }

    public function listSongs()
    {
        return $this->response->json();
    }

    public function showSong()
    {
        return $this->response->json();
    }

    public function createSong()
    {
        return $this->response->json();
    }

    public function updateSong()
    {
        return $this->response->json();
    }

    public function deleteSong()
    {
        return $this->response->json();
    }
}
