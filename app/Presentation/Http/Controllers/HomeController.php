<?php

namespace App\Presentation\Http\Controllers;

use App\Application\UseCase\Public\RankSongs;
use App\Application\UseCase\Public\SuggestSong;
use App\Domain\Entity\Song;
use App\Domain\ValueObject\YoutubeLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

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

        $responseContent = array_map(
            fn (array $song) => Arr::only($song, ['title', 'thumbnailUrl', 'viewsCount']),
            $songs->toArray()
        );

        $this->response->setData($responseContent);
        return $this->response;
    }

    public function suggestSong(): JsonResponse
    {
        $link = new YoutubeLink($this->request->string('link'));

        app()->terminating(
            fn () => ($this->suggestSongUseCase)($link)
        );

        return $this->response;
    }
}
