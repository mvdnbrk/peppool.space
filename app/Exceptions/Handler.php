<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class Handler extends ExceptionHandler
{
    public function register(): void
    {
        $this->renderable(function (RpcResponseException $e, Request $request): JsonResponse {
            $payload = [
                'message' => $e->getMessage(),
                'method' => $e->method,
                'rpcCode' => $e->rpcCode,
            ];

            if (app()->hasDebugModeEnabled()) {
                $payload['httpStatus'] = $e->httpStatus;
                $payload['response'] = [
                    'headers' => $e->response?->headers(),
                    'body' => $this->extractResponseBody($e),
                ];
            }

            return response()->json(
                $payload,
                $e->httpStatus ?: Response::HTTP_BAD_GATEWAY
            );
        });
    }

    private function extractResponseBody(RpcResponseException $e): mixed
    {
        if ($e->response === null) {
            return null;
        }

        $json = $e->response->json();
        if (! is_null($json)) {
            return $json;
        }

        return Str::of((string) $e->response->body())
            ->limit(2000, '…')
            ->toString();
    }
}
