<?php

namespace App\Presentation\Http\Controllers;

use App\Application\UseCase\Public\RankSongs;
use App\Application\UseCase\Public\SuggestSong;
use App\Domain\ValueObject\YoutubeLink;
use App\Presentation\Http\Request\RankSongsRequest;
use App\Presentation\Http\Request\SuggestSongRequest;
use App\Presentation\Http\Response\RankSongsResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController
{
    public function __construct(
        protected Request $request,
        protected JsonResponse $response,
        protected SuggestSong $suggestSongUseCase,
        protected RankSongs $rankSongsUseCase,
    ) {}

    public function rankSongs(RankSongsRequest $input): JsonResponse
    {
        $songs = ($this->rankSongsUseCase)($input->page, $input->perPage);

        $output = RankSongsResponse::collect($songs->toArray());

        $this->response->setData($output);

        return $this->response;
    }

    public function suggestSong(SuggestSongRequest $input): JsonResponse
    {
        $link = new YoutubeLink($input->link);

        app()->terminating(
            fn () => ($this->suggestSongUseCase)($link)
        );

        $this->response->setStatusCode(200);

        return $this->response;
    }
}
