<?php

namespace App\Application\UseCase\Public;

use App\Domain\Entity\Song;
use App\Domain\Exception\SongAlreadyExistsException;
use App\Domain\Repository\SongMetadataRetriever;
use App\Domain\Repository\SongRepository;
use App\Domain\ValueObject\YoutubeLink;

class SuggestSong
{
    public function __construct(
        protected SongMetadataRetriever $metadataRetriever,
        protected SongRepository $songRepository,
    )
    {
    }

    public function __invoke(YoutubeLink $link): void
    {
        if ($this->songRepository->find($link->id())) {
            throw new SongAlreadyExistsException();
        }

        $metadata = $this->metadataRetriever->retrieve($link);

        $song = new Song(
            id: $link->id(),
            title: $metadata->title,
            viewsCount: $metadata->viewsCount,
        );

        $this->songRepository->save($song);
    }
}
