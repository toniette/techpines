<?php

namespace App\Domain\Repository;

use App\Domain\DataTransferObject\SongMetadata;
use App\Domain\ValueObject\YoutubeLink;

interface SongMetadataRetriever
{
    public function retrieve(YoutubeLink $link): SongMetadata;
}
