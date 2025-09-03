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
use App\Domain\Exception\InvalidSongDataException;
use App\Domain\ValueObject\YoutubeLink;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;
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
            $page = $this->request->integer('page', 1);
            $perPage = $this->request->integer('perPage', 10);

            if ($page < 1 || $perPage < 1 || $perPage > 50) {
                $this->response->setStatusCode(422);
                return $this->response;
            }

            $params = array_filter([
                'page' => $page,
                'perPage' => $perPage,
                'sortBy' => SongSortableProperty::tryFrom($this->request->string('sortBy')),
                'direction' => SongSortDirection::tryFrom($this->request->string('direction')),
                'filterBy' => SongFilterableProperty::tryFrom($this->request->string('filterBy')),
                'filterValue' => $this->request->string('filterValue')
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
        $link = new YoutubeLink($this->request->string('link'));

        $song = ($this->addSongUseCase)($link);

        $this->response->setStatusCode(201);
        $this->response->setContent($song->toArray());
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
        ($this->rejectSongUseCase)($id);

        return $this->response;
    }
}
