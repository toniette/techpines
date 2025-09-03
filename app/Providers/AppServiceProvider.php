<?php

namespace App\Providers;

use App\Domain\Repository\SongMetadataRetriever;
use App\Domain\Repository\SongRepository;
use App\Infrastructure\ApiSongMetadataRetriever;
use App\Infrastructure\Repository\EloquentSongRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SongMetadataRetriever::class, ApiSongMetadataRetriever::class);
        $this->app->bind(SongRepository::class, EloquentSongRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
