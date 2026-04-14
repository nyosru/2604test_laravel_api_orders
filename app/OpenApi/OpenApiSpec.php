<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Laravel 12 Orders API',
    description: 'Minimal API documentation generated with L5 Swagger.'
)]
#[OA\Server(
    url: '/',
    description: 'текущий домен Docker Laravel'
)]
#[OA\Tag(
    name: 'Health',
    description: 'Application health endpoints'
)]
class OpenApiSpec
{
}
