<?php

namespace App\Application\UseCase\Protected;

use App\Domain\Entity\Song;
use App\Domain\Entity\User;
use App\Domain\Exception\SongAlreadyExistsException;
use App\Domain\Repository\SongMetadataRetriever;
use App\Domain\Repository\SongRepository;
use App\Domain\ValueObject\YoutubeLink;

class AddSong
{
    public function __construct(
        protected SongMetadataRetriever $metadataRetriever,
        protected SongRepository $songRepository,
        protected User $user,
    )
    {
    }

    public function __invoke(YoutubeLink $link, bool $approve = false): Song
    {
        if ($this->songRepository->find($link->id())) {
            throw new SongAlreadyExistsException();
        }

        $metadata = $this->metadataRetriever->retrieve($link);

        $song = $this->user->addSong($metadata, $approve);

        $this->songRepository->save($song);

        return $song;
    }
}
