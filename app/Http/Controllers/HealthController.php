<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

#[OA\Get(
    path: '/',
    operationId: 'healthCheck',
    summary: 'Health check route',
    tags: ['Health'],
    responses: [
        new OA\Response(
            response: 200,
            description: 'Application is running',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'app', type: 'string', example: 'Laravel 12 Orders API'),
                    new OA\Property(property: 'status', type: 'string', example: 'ok'),
                ],
                type: 'object'
            )
        ),
    ]
)]
class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'app' => 'Laravel 12 Orders API',
            'status' => 'ok',
        ]);
    }
}
