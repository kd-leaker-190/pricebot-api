<?php

use App\Http\Controllers\ApiController;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$apiController = new ApiController();

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
    ->withExceptions(function (Exceptions $exceptions) use ($apiController) {
        $exceptions->render(function (ModelNotFoundException $ex) use ($apiController) {
            return $apiController->errorResponse($ex->getMessage(), 404);
        });

        $exceptions->render(function (NotFoundHttpException $ex) use ($apiController) {
            return $apiController->errorResponse($ex->getMessage(), 404);
        });

        $exceptions->render(function (AuthenticationException $ex) use ($apiController) {
            return $apiController->errorResponse('Unauthenticated.', 401);
        });

        $exceptions->render(function (AuthorizationException $ex) use ($apiController) {
            return $apiController->errorResponse('Forbidden.', 403);
        });

        $exceptions->render(function (MethodNotAllowedHttpException $ex) use ($apiController) {
            return $apiController->errorResponse('Method Not Allowed.', 405);
        });

        $exceptions->render(function (QueryException $ex) use ($apiController) {
            return $apiController->errorResponse('Database error: ' . $ex->getMessage(), 500);
        });

        $exceptions->render(function (HttpExceptionInterface $ex) use ($apiController) {
            return $apiController->errorResponse($ex->getMessage(), $ex->getStatusCode());
        });

        $exceptions->render(function (Throwable $ex) use ($apiController) {
            Log::error($ex);
        });
    })->create();
