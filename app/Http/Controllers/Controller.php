<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $statusCode = HttpResponse::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function errorResponse(
        string $message = 'Error occurred',
        mixed $errors = null,
        int $statusCode = HttpResponse::HTTP_BAD_REQUEST
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    protected function noContentResponse(): Response
    {
        return response()->noContent();
    }

    protected function createdResponse(
        mixed $data = null,
        string $message = 'Created successfully'
    ): JsonResponse {
        return $this->successResponse($data, $message, HttpResponse::HTTP_CREATED);
    }

    protected function notFoundResponse(string $message = 'Resource not found'): JsonResponse
    {
        return $this->errorResponse($message, null, HttpResponse::HTTP_NOT_FOUND);
    }

    protected function unauthorizedResponse(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->errorResponse($message, null, HttpResponse::HTTP_UNAUTHORIZED);
    }

    protected function forbiddenResponse(string $message = 'Forbidden'): JsonResponse
    {
        return $this->errorResponse($message, null, HttpResponse::HTTP_FORBIDDEN);
    }

    protected function validationErrorResponse(mixed $errors): JsonResponse
    {
        return $this->errorResponse(
            'Validation failed',
            $errors,
            HttpResponse::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
