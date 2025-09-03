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
use App\Domain\Enum\SongFilterableProperty;
use App\Domain\Enum\SongSortableProperty;
use App\Domain\Enum\SongSortDirection;
use App\Domain\ValueObject\YoutubeLink;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ValueError;

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
    }

    public function listSongs()
    {
        try {
            $params = array_filter([
                'page' => $this->request->integer('page', 1),
                'perPage' => $this->request->integer('per_page', 10),
                'sortBy' => SongSortableProperty::tryFrom($this->request->string('sort_by')),
                'direction' => SongSortDirection::tryFrom($this->request->string('direction')),
                'filterBy' => SongFilterableProperty::tryFrom($this->request->string('filter_by')),
                'filterValue' => $this->request->string('filter_value')
            ]);
        } catch (ValueError $e) {
            $this->response->setStatusCode(422);
            return $this->response;
        }

        $songs = ($this->listSongsUseCase)(...$params);

        $this->response->setContent($songs->toArray());
        return $this->response;
    }

    public function showSong(string $id)
    {
        $song = ($this->readSongUseCase)($id);

        $this->response->setContent($song->toArray());
        return $this->response;
    }

    public function addSong()
    {
        $link = new YoutubeLink($this->request->input('link'));

        $song = ($this->addSongUseCase)($link);

        $this->response->setStatusCode(201);
        $this->response->setContent($song->toArray());
        return $this->response;
    }

    public function updateSong(string $id)
    {
        $data = new SongMetadata(
            title: $this->request->input('title'),
            thumbnailUrl: $this->request->input('thumbnail_url'),
            viewsCount: $this->request->integer('views_count'),
        );

        $song = ($this->updateSongUseCase)($id, $data);

        $this->response->setContent($song->toArray());
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
        ($this->approveSongUseCase)($id);

        return $this->response;
    }
}
