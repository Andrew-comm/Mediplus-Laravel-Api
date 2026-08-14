<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Mediplus Pharmacy API",
    description: "REST API for the Mediplus Pharmacy Management System.",
    contact: new OA\Contact(
        name: "Mediplus Development Team"
    )
)]

#[OA\Server(
    url: "http://127.0.0.1:8001",
    description: "Mediplus Local Development Server"
)]

class OpenApiSpec
{
}
