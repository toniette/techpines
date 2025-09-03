<?php

namespace App\Presentation\Http\Controllers;

use App\Application\UseCase\Public\RankSongs;
use App\Application\UseCase\Public\SuggestSong;
use App\Domain\ValueObject\YoutubeLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController
{
    public function __construct(
        protected Request      $request,
        protected JsonResponse $response,
        protected SuggestSong  $suggestSongUseCase,
        protected RankSongs    $rankSongsUseCase,
    )
    {
    }

    public function listSongs(): JsonResponse
    {
        $songs = ($this->rankSongsUseCase)(
            $this->request->integer('page', 1),
            $this->request->integer('per_page', 10)
        );

        $this->response->setData($songs->toArray());
        return $this->response;
    }

    public function suggestSong(): JsonResponse
    {
        $link = new YoutubeLink($this->request->input('link'));
        ($this->suggestSongUseCase)($link);

        return $this->response;
    }
}
