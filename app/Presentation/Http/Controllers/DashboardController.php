<?php

namespace App\Presentation\Http\Controllers;

use App\Application\UseCase\Protected\AddSong;
use App\Application\UseCase\Protected\ApproveSong;
use App\Application\UseCase\Protected\DeleteSong;
use App\Application\UseCase\Protected\ListSongs;
use App\Application\UseCase\Protected\ReadSong;
use App\Application\UseCase\Protected\RejectSong;
use App\Application\UseCase\Protected\UpdateSong;
use App\Domain\DataTransferObject\SongMetadata;
use App\Domain\Entity\User;
use App\Domain\ValueObject\YoutubeLink;
use App\Presentation\Http\Request\AddSongRequest;
use App\Presentation\Http\Request\ListSongsRequest;
use App\Presentation\Http\Response\ListSongsResponse;
use App\Presentation\Http\Response\SongResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class DashboardController
{
    public function __construct(
        protected Request $request,
        protected Response $response,
        protected User $user,
        protected ListSongs $listSongsUseCase,
        protected ReadSong $readSongUseCase,
        protected AddSong $addSongUseCase,
        protected UpdateSong $updateSongUseCase,
        protected DeleteSong $deleteSongUseCase,
        protected ApproveSong $approveSongUseCase,
        protected RejectSong $rejectSongUseCase,
    )
    {
        $this->response->setContent([]);
    }

    public function listSongs(ListSongsRequest $input)
    {
        $songs = ($this->listSongsUseCase)(
            page: $input->page,
            perPage: $input->perPage,
            sortBy: $input->sortBy,
            direction: $input->direction,
            filterBy: $input->filterBy,
            filterValue: $input->filterValue,
        );

        $output = ListSongsResponse::collect($songs->toArray());
        $this->response->setContent($output);
        return $this->response;
    }

    public function showSong(string $id)
    {
        $song = ($this->readSongUseCase)($id);

        $output = SongResponse::from($song->toArray());
        $this->response->setContent($output);
        return $this->response;
    }

    public function addSong(AddSongRequest $input)
    {
        $link = new YoutubeLink($input->link);

        $song = ($this->addSongUseCase)($link);

        $this->response->setStatusCode(201);
        $output = SongResponse::from($song->toArray());
        $this->response->setContent($output);
        return $this->response;
    }

    public function updateSong(string $id)
    {
        try {
            $data = new SongMetadata(
                title: $this->request->string('title'),
                thumbnailUrl: $this->request->string('thumbnailUrl'),
                viewsCount: $this->request->input('viewsCount'),
            );
        } catch (Throwable) {
            $this->response->setStatusCode(422);
            return $this->response;
        }

        $song = ($this->updateSongUseCase)($id, $data);

        $output = SongResponse::from($song->toArray());
        $this->response->setContent($output);
        return $this->response;
    }

    public function deleteSong(string $id)
    {
        ($this->deleteSongUseCase)($id);

        $this->response->setStatusCode(204);
        return $this->response;
    }

    public function approveSong(string $id)
    {
        ($this->approveSongUseCase)($id);

        return $this->response;
    }

    public function rejectSong(string $id)
    {
        ($this->rejectSongUseCase)($id);

        return $this->response;
    }
}
