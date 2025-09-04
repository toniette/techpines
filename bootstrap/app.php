<?php

use App\Domain\Exception\InvalidYoutubeLinkException;
use App\Domain\Exception\SongAlreadyExistsException;
use App\Domain\Exception\SongNotFoundException;
use App\Domain\State\InvalidTransitionException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (InvalidTransitionException $e) {
            return response()->json([], 400);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([], 401);
        });

        $exceptions->render(function (SongNotFoundException $e) {
            return response()->json([], 404);
        });

        $exceptions->render(function (MethodNotAllowedException $e) {
            return response()->json([], 405);
        });

        $exceptions->render(function (SongAlreadyExistsException $e) {
            return response()->json([], 409);
        });

        $exceptions->render(function (InvalidYoutubeLinkException|ValidationException $e) {
            return response()->json([], 422);
        });

        $exceptions->render(function (Throwable $e) {
            dd($e);
            return response()->json([], 500);
        });
    })->create();
